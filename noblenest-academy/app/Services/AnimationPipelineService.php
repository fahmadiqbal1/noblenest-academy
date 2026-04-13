<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\ActivityStep;
use App\Models\AIProviderConfig;
use App\Models\MaternalContent;
use App\Models\MaternalContentStep;
use Illuminate\Support\Facades\Log;

class AnimationPipelineService
{
    private AIProviderGateway $gateway;
    private VideoGenerationService $tts;

    public function __construct(AIProviderGateway $gateway, VideoGenerationService $tts)
    {
        $this->gateway = $gateway;
        $this->tts = $tts;
    }

    /**
     * Generate illustration for a step and return the stored path.
     */
    public function generateStepIllustration(string $title, string $instruction, string $context = ''): ?string
    {
        $provider = $this->getImageProvider();
        if (!$provider) {
            Log::warning('AnimationPipeline: No image provider configured.');
            return null;
        }

        $prompt = $this->buildImagePrompt($title, $instruction, $context);

        try {
            $result = $this->gateway->generateImage($provider, $prompt, [
                'timeout' => 120,
            ]);

            return $result['path'] ?? null;
        } catch (\Throwable $e) {
            Log::error('AnimationPipeline: Image generation failed', [
                'error' => $e->getMessage(),
                'title' => $title,
            ]);
            return null;
        }
    }

    /**
     * Generate TTS narration for a step and return the stored path.
     */
    public function generateStepNarration(string $text, string $language = 'en'): ?string
    {
        try {
            return $this->tts->generateSpeech($text, $language);
        } catch (\Throwable $e) {
            Log::error('AnimationPipeline: TTS generation failed', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Process a single maternal content step: illustration + narration.
     */
    public function processStep(MaternalContentStep $step): void
    {
        $content = $step->content;
        $context = ($content->category ?? '') . ' — ' . ($content->stage ?? '');

        if (!$step->visual_url) {
            $visualPath = $this->generateStepIllustration(
                $step->title,
                $step->instruction,
                $context
            );
            if ($visualPath) {
                $step->update(['visual_url' => $visualPath]);
            }
        }

        if (!$step->audio_url) {
            $narration = $step->instruction;
            if ($step->tip) {
                $narration .= ' Tip: ' . $step->tip;
            }
            $audioPath = $this->generateStepNarration($narration);
            if ($audioPath) {
                $step->update(['audio_url' => $audioPath]);
            }
        }
    }

    /**
     * Process a single activity step: illustration + narration.
     */
    public function processActivityStep(ActivityStep $step): void
    {
        $activity = $step->activity;
        $context = ($activity->subject ?? '') . ' — ' . ($activity->age_group ?? '');

        if (!$step->visual_url) {
            $visualPath = $this->generateStepIllustration(
                $step->title,
                $step->instruction,
                $context
            );
            if ($visualPath) {
                $step->update(['visual_url' => $visualPath]);
            }
        }

        if (!$step->audio_url) {
            $narration = $step->instruction;
            if ($step->benefit_note) {
                $narration .= ' ' . $step->benefit_note;
            }
            $audioPath = $this->generateStepNarration($narration);
            if ($audioPath) {
                $step->update(['audio_url' => $audioPath]);
            }
        }
    }

    /**
     * Build an illustration prompt from step content.
     */
    private function buildImagePrompt(string $title, string $instruction, string $context): string
    {
        $base = "Create a warm, professional educational illustration for a maternal wellness guide. ";
        $base .= "The illustration should be calming, using soft pastel colors (lavender, mint, cream). ";
        $base .= "Style: clean, modern, medical-illustration quality, suitable for expectant mothers. ";
        $base .= "No text in the image. ";
        $base .= "Topic: {$title}. ";
        $base .= "Context: {$context}. ";
        $base .= "Depicting: " . \Illuminate\Support\Str::limit($instruction, 200);

        return $base;
    }

    /**
     * Find the first active image-capable provider (prefer Gemini for free tier).
     */
    private function getImageProvider(): ?AIProviderConfig
    {
        // Prefer Gemini (free tier)
        $gemini = AIProviderConfig::where('provider_name', 'like', '%gemini%')
            ->where('is_active', true)
            ->first();

        if ($gemini) {
            return $gemini;
        }

        // Fall back to any image-capable provider
        return AIProviderConfig::where('is_active', true)
            ->whereIn('provider_name', ['stability', 'openai-image', 'openai'])
            ->first();
    }
}
