<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AIJob;
use App\Models\AIProviderConfig;
use App\Models\Activity;
use App\Services\AIProviderGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * AI Orchestrator — the "agent in charge" that designs and improves the curriculum.
 *
 * Supports multiple pluggable AI providers (OpenAI, Anthropic, Gemini, GitHub Repo,
 * or any custom API). Staff can add providers, queue generation jobs, review/approve
 * results, and publish generated content with a single click.
 */
class OrchestratorController extends Controller
{
    public function __construct(protected AIProviderGateway $gateway)
    {
    }

    // ------------------------------------------------------------------
    // Dashboard
    // ------------------------------------------------------------------

    public function index(Request $request)
    {
        $providers = AIProviderConfig::orderBy('name')->get();
        $jobs      = AIJob::with('user')
                          ->orderByDesc('created_at')
                          ->paginate(20);

        $stats = [
            'queued'    => AIJob::where('status', 'queued')->count(),
            'running'   => AIJob::where('status', 'running')->count(),
            'completed' => AIJob::where('status', 'completed')->count(),
            'failed'    => AIJob::where('status', 'failed')->count(),
            'pending_moderation' => AIJob::where('moderation_status', 'pending')
                                         ->where('status', 'completed')
                                         ->count(),
        ];

        $jobTypes = [
            'lesson_plan'  => 'Generate Lesson Plan',
            'activity'     => 'Generate Activity',
            'translation'  => 'Translate Content',
            'video_lesson' => 'Generate Video Lesson',
            'tts'          => 'Text-to-Speech',
            'image'        => 'Generate Illustration',
            'quiz'         => 'Generate Quiz',
            'curriculum_review' => 'Review Curriculum',
            'github_extract'    => 'Extract from GitHub Repo',
        ];

        $locales = ['en' => 'English', 'fr' => 'French', 'ru' => 'Russian',
                    'zh' => 'Mandarin', 'es' => 'Spanish', 'ko' => 'Korean'];

        return view('admin.orchestrator.index', compact(
            'providers', 'jobs', 'stats', 'jobTypes', 'locales'
        ));
    }

    // ------------------------------------------------------------------
    // Provider management
    // ------------------------------------------------------------------

    public function storeProvider(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:100',
            'slug'         => 'required|string|max:60|unique:ai_provider_configs,slug',
            'driver'       => 'nullable|string|in:openai,anthropic,gemini,github,stability,elevenlabs,replicate,runway,openai-image',
            'api_base_url' => 'nullable|url|max:255',
            'api_key'      => 'nullable|string|max:500',
            'model'        => 'nullable|string|max:100',
            'capabilities' => 'nullable|array',
            'repo_url'     => 'nullable|url|max:255',
        ]);

        $provider = AIProviderConfig::create([
            'name'             => $data['name'],
            'slug'             => $data['slug'],
            'api_base_url'     => $data['api_base_url'] ?? null,
            'api_key_encrypted'=> isset($data['api_key']) && $data['api_key']
                                    ? Crypt::encryptString($data['api_key'])
                                    : null,
            'model'            => $data['model'] ?? null,
            'is_active'        => true,
            'connection_status'=> 'unchecked',
            'capabilities'     => $data['capabilities'] ?? [],
            'extra_config'     => array_filter([
                'driver' => $data['driver'] ?? null,
                'repo_url' => $data['repo_url'] ?? null,
            ]),
        ]);

        $health = $this->syncProviderStatus($provider);

        return back()->with('status', 'Provider "' . $data['name'] . '" added. Status: ' . $health['message']);
    }

    public function destroyProvider(AIProviderConfig $provider)
    {
        $provider->delete();
        return back()->with('status', 'Provider removed.');
    }

    public function toggleProvider(AIProviderConfig $provider)
    {
        $provider->update(['is_active' => ! $provider->is_active]);
        return back()->with('status', 'Provider ' . ($provider->is_active ? 'enabled' : 'disabled') . '.');
    }

    public function verifyProvider(AIProviderConfig $provider)
    {
        $health = $this->syncProviderStatus($provider);

        return back()->with('status', 'Provider "' . $provider->name . '" check complete. ' . $health['message']);
    }

    // ------------------------------------------------------------------
    // Job dispatch
    // ------------------------------------------------------------------

    public function dispatchJob(Request $request)
    {
        $data = $request->validate([
            'type'     => 'required|string',
            'locale'   => 'required|string|max:8',
            'provider' => 'nullable|string|exists:ai_provider_configs,slug',
            'prompt'   => 'required|string|max:5000',
            'context'  => 'nullable|string|max:2000',
            'repo_url' => 'nullable|url',
        ]);

        $job = AIJob::create([
            'type'              => $data['type'],
            'status'            => 'queued',
            'provider'          => $data['provider'] ?? 'mock',
            'locale'            => $data['locale'],
            'user_id'           => Auth::id(),
            'moderation_status' => 'pending',
            'payload'           => [
                'prompt'   => $data['prompt'],
                'context'  => $data['context'] ?? null,
                'repo_url' => $data['repo_url'] ?? null,
            ],
        ]);

        // Attempt immediate execution (synchronous for simple/mock providers)
        $this->runJob($job);

        return back()->with('status', 'Job #' . $job->id . ' dispatched.');
    }

    // ------------------------------------------------------------------
    // Moderation
    // ------------------------------------------------------------------

    public function approve(AIJob $job)
    {
        $job->update(['moderation_status' => 'approved']);

        // If job produced activity content, auto-create an activity
        $this->publishJobResult($job);

        return back()->with('status', 'Job #' . $job->id . ' approved and published.');
    }

    public function reject(AIJob $job)
    {
        $job->update(['moderation_status' => 'rejected']);
        return back()->with('status', 'Job #' . $job->id . ' rejected.');
    }

    public function retryJob(AIJob $job)
    {
        $job->update(['status' => 'queued', 'error_message' => null,
                      'started_at' => null, 'completed_at' => null]);
        $this->runJob($job);
        return back()->with('status', 'Job #' . $job->id . ' re-queued.');
    }

    public function destroyJob(AIJob $job)
    {
        $job->delete();
        return back()->with('status', 'Job deleted.');
    }

    // ------------------------------------------------------------------
    // Orchestrator: curriculum health scan
    // ------------------------------------------------------------------

    public function scanCurriculum(Request $request)
    {
        $gaps        = [];
        $ageRanges   = range(0, 10);
        $requiredSkills = [
            'Language & Literacy', 'Numeracy', 'Cognitive', 'Fine Motor',
            'Gross Motor', 'Social-Emotional', 'Creative Arts', 'STEM',
        ];

        foreach ($ageRanges as $age) {
            foreach ($requiredSkills as $skill) {
                $count = Activity::where('subject', $skill)
                    ->where('age_min', '<=', $age)
                    ->where('age_max', '>=', $age)
                    ->count();
                if ($count === 0) {
                    $gaps[] = ['age' => $age, 'skill' => $skill, 'count' => 0];
                }
            }
        }

        return response()->json([
            'gaps'        => $gaps,
            'total_gaps'  => count($gaps),
            'suggestion'  => count($gaps)
                ? 'Run the Orchestrator to generate activities for the identified gaps.'
                : 'Curriculum looks well-covered! Keep adding more variety.',
        ]);
    }

    // ------------------------------------------------------------------
    // Internal helpers
    // ------------------------------------------------------------------

    protected function runJob(AIJob $job): void
    {
        $job->update(['status' => 'running', 'started_at' => now()]);

        try {
            $result = $this->callProvider($job);
            $job->update([
                'status'       => 'completed',
                'result'       => $result,
                'completed_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('AIJob #' . $job->id . ' failed: ' . $e->getMessage());
            $job->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at'  => now(),
            ]);
        }
    }

    protected function callProvider(AIJob $job): array
    {
        $providerSlug = $job->provider ?? 'mock';
        $payload      = $job->payload ?? [];
        $prompt       = $payload['prompt'] ?? '';

        // GitHub repo extraction
        if ($job->type === 'github_extract' && ! empty($payload['repo_url'])) {
            return $this->extractFromGithub($payload['repo_url'], $prompt);
        }

        // Explicit mock
        if ($providerSlug === 'mock') {
            return $this->mockGenerate($job->type, $prompt, $job->locale);
        }

        // Require a configured, active provider
        $config = AIProviderConfig::where('slug', $providerSlug)
                                  ->where('is_active', true)
                                  ->first();

        if (! $config || ! $config->api_key_encrypted) {
            throw new \RuntimeException("Provider '{$providerSlug}' is not configured or has no API key.");
        }

        $systemPrompt = "You are Noble Nest Academy's expert curriculum designer. Generate age-appropriate, safe, and pedagogically sound educational content. Output in {$job->locale} unless instructed otherwise. Keep content free of violence, adult themes, or harmful material.";

        // Route by job type to correct gateway method
        if (in_array($job->type, ['image'], true)) {
            return $this->gateway->generateImage($config, $prompt);
        }

        if (in_array($job->type, ['tts'], true)) {
            return $this->gateway->generateAudio($config, $prompt);
        }

        if (in_array($job->type, ['video', 'video_lesson'], true)) {
            return $this->gateway->generateVideo($config, $prompt);
        }

        return $this->gateway->chat($config, $prompt, [
            'system_prompt' => $systemPrompt,
            'max_tokens'    => 1200,
            'temperature'   => 0.5,
        ]);
    }

    protected function extractFromGithub(string $repoUrl, string $prompt): array
    {
        // Extract owner/repo from URL
        preg_match('#github\.com/([^/]+/[^/]+)#', $repoUrl, $m);
        $repo = $m[1] ?? null;

        if (! $repo) {
            throw new \InvalidArgumentException('Invalid GitHub URL: ' . $repoUrl);
        }

        // Fetch README via GitHub raw API (no auth needed for public repos)
        $readmeUrl = "https://raw.githubusercontent.com/{$repo}/HEAD/README.md";
        $response  = Http::timeout(15)->get($readmeUrl);

        $content = $response->successful()
            ? substr($response->body(), 0, 8000)
            : '(README not available)';

        return [
            'repo'    => $repo,
            'readme'  => $content,
            'summary' => "Extracted README from {$repo}. Review and incorporate relevant curriculum ideas.",
            'prompt'  => $prompt,
        ];
    }

    protected function mockGenerate(string $type, string $prompt, string $locale): array
    {
        $templates = [
            'lesson_plan'  => "**Mock Lesson Plan** ({$locale})\n\nObjectives: ...\nMaterials: ...\nSteps: ...\n\nPrompt used: {$prompt}",
            'activity'     => "**Mock Activity**\n\nTitle: Placeholder\nAge: 3–5\nDuration: 15 min\nInstructions: 1. Do this 2. Do that\n\nPrompt: {$prompt}",
            'translation'  => "(Mock translation to {$locale}) " . $prompt,
            'video_lesson' => "**Mock Video Script**\n\nScene 1: ...\nScene 2: ...\n\nNarration: {$prompt}",
            'tts'          => "TTS audio would be generated from: {$prompt}",
            'image'        => "Image prompt sent to generation API: {$prompt}",
            'quiz'         => "**Mock Quiz**\n\nQ1: ...\nA) ... B) ... C) ...\nCorrect: A\n\nGenerated from: {$prompt}",
            'curriculum_review' => "**Curriculum Review**\n\nStrengths: ...\nGaps: ...\nRecommendations: ...\n\nContext: {$prompt}",
            'github_extract'    => "GitHub extraction would fetch repo content for: {$prompt}",
        ];

        return [
            'content'  => $templates[$type] ?? "Generated content for type '{$type}':\n\n{$prompt}",
            'provider' => 'mock',
            'locale'   => $locale,
        ];
    }

    protected function publishJobResult(AIJob $job): void
    {
        $result  = $job->result ?? [];
        $content = $result['content'] ?? null;
        $type    = $result['type'] ?? 'text';

        if (! $content || $job->type === 'github_extract') {
            return;
        }

        if (! in_array($job->type, ['activity', 'lesson_plan', 'quiz', 'image', 'tts', 'video', 'video_lesson'], true)) {
            return;
        }

        // Try to parse JSON content (LLM may return structured data)
        $parsed = null;
        if (str_starts_with(trim($content), '{')) {
            $parsed = json_decode($content, true);
        }

        $title = $parsed['title'] ?? ('AI Generated: ' . ucfirst($job->type) . ' #' . $job->id);

        $activityData = [
            'title'               => $title,
            'description'         => $parsed['description'] ?? $content,
            'language'            => $job->locale,
            'activity_type'       => match($job->type) {
                'quiz'                    => 'quiz',
                'video', 'video_lesson'   => 'video',
                'image'                   => 'image',
                'tts'                     => 'audio',
                default                   => 'lesson',
            },
            'age_min'             => $parsed['age_min'] ?? 0,
            'age_max'             => $parsed['age_max'] ?? 10,
            'subject'             => $parsed['subject'] ?? null,
            'age_group'           => $parsed['age_tier'] ?? $parsed['age_group'] ?? null,
            'duration_minutes'    => $parsed['duration_minutes'] ?? null,
            'difficulty'          => $parsed['difficulty'] ?? null,
            'instructions'        => $parsed['instructions'] ?? null,
            'materials_needed'    => isset($parsed['materials']) ? (array) $parsed['materials'] : null,
            'learning_objectives' => isset($parsed['learning_objectives']) ? (array) $parsed['learning_objectives'] : null,
            'is_muslim_only'      => $parsed['is_muslim_only'] ?? false,
            'is_free'             => $parsed['is_free'] ?? true,
        ];

        // Attach generated media URL if present
        if (isset($result['url'])) {
            if ($type === 'image') {
                $activityData['thumbnail_url'] = $result['url'];
            } elseif (in_array($type, ['audio', 'video'])) {
                $activityData['media_url'] = $result['url'];
            }
        }

        Activity::create($activityData);
    }

    protected function syncProviderStatus(AIProviderConfig $provider): array
    {
        $health = $this->gateway->verify($provider);
        $now = now();

        $provider->update([
            'connection_status' => $health['status'],
            'connection_message' => $health['message'],
            'last_checked_at' => $now,
            'last_live_at' => $health['status'] === 'live' ? $now : $provider->last_live_at,
        ]);

        return $health;
    }
}
