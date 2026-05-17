<?php

namespace App\Console\Commands;

use App\Models\AIProviderConfig;
use App\Models\Course;
use App\Services\AIProviderGateway;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateOGImagesCommand extends Command
{
    protected $signature = 'og:generate
        {--page= : Generate for a specific page (home, marketplace, or a course slug)}
        {--all : Generate for all pages}
        {--provider= : AI provider config ID}';

    protected $description = 'Generate Open Graph images for the marketplace, homepage, and courses';

    public function handle(AIProviderGateway $gateway): int
    {
        $provider = $this->resolveProvider();
        if (! $provider) {
            $this->error('No active image-capable provider found. Add one in the Orchestrator or pass --provider=ID.');

            return self::FAILURE;
        }

        $pages = $this->resolvePages();
        if (empty($pages)) {
            $this->error('No pages to generate. Use --all or --page=<name>.');

            return self::FAILURE;
        }

        $bar = $this->output->createProgressBar(count($pages));
        $bar->start();

        foreach ($pages as $page) {
            try {
                $prompt = $this->buildPrompt($page);
                $result = $gateway->generateImage($provider, $prompt);

                if (! empty($result['path'])) {
                    $dest = 'public/og/'.Str::slug($page['slug']).'.png';
                    if (Storage::exists($result['path'])) {
                        Storage::copy($result['path'], $dest);
                    }
                    $this->line(" Generated: {$dest}");
                }
            } catch (\Throwable $e) {
                $this->warn(" Failed for {$page['slug']}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('OG image generation complete.');

        return self::SUCCESS;
    }

    private function resolveProvider(): ?AIProviderConfig
    {
        if ($id = $this->option('provider')) {
            return AIProviderConfig::find($id);
        }

        return AIProviderConfig::where('is_active', true)
            ->where(function ($q) {
                $q->whereJsonContains('capabilities', 'image')
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(extra_config, '$.driver')) IN ('gemini', 'stability', 'openai-image')");
            })->first();
    }

    private function resolvePages(): array
    {
        $pages = [];

        if ($this->option('all')) {
            $pages[] = ['slug' => 'home', 'title' => 'Noble Nest Academy', 'desc' => 'Where little minds blossom — early childhood education platform'];
            $pages[] = ['slug' => 'marketplace', 'title' => 'Course Marketplace', 'desc' => 'Browse age-appropriate courses for children 0-11'];

            foreach (Course::all() as $course) {
                $pages[] = [
                    'slug' => 'course-'.Str::slug($course->title),
                    'title' => $course->title,
                    'desc' => Str::limit($course->description, 100),
                ];
            }
        } elseif ($page = $this->option('page')) {
            if ($page === 'home') {
                $pages[] = ['slug' => 'home', 'title' => 'Noble Nest Academy', 'desc' => 'Where little minds blossom'];
            } elseif ($page === 'marketplace') {
                $pages[] = ['slug' => 'marketplace', 'title' => 'Course Marketplace', 'desc' => 'Browse courses for children'];
            } else {
                $course = Course::where('slug', $page)->orWhere('id', $page)->first();
                if ($course) {
                    $pages[] = [
                        'slug' => 'course-'.Str::slug($course->title),
                        'title' => $course->title,
                        'desc' => Str::limit($course->description, 100),
                    ];
                }
            }
        }

        return $pages;
    }

    private function buildPrompt(array $page): string
    {
        return "Create a professional Open Graph social media preview image (1200x630px) for '{$page['title']}'. "
            ."Description: {$page['desc']}. "
            .'Style: Blossom design with soft pastels (#2563EB blue, #F97316 orange, #F8FAFC background), '
            .'Claymorphism 3D elements, child-friendly, educational theme. '
            .'Include subtle Noble Nest Academy branding. No text overlay needed.';
    }
}
