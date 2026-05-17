<?php

namespace App\Jobs;

use App\Models\ActivityMedia;
use App\Services\Providers\WhisperAdapter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Phase 6 — generate a VTT subtitle file for one ActivityMedia row
 * via the bound WhisperAdapter, then attach as a sibling ActivityMedia.
 */
class GenerateSubtitlesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    /** @var array<int,int> */
    public array $backoff = [30, 120, 300];

    public function __construct(public readonly int $mediaId, public readonly string $locale)
    {
    }

    public function handle(WhisperAdapter $whisper): void
    {
        $media = ActivityMedia::find($this->mediaId);
        if (! $media) {
            Log::warning('GenerateSubtitlesJob: media missing', ['id' => $this->mediaId]);

            return;
        }

        try {
            $vtt = $whisper->transcribeToVtt((string) $media->url, $this->locale);
        } catch (\Throwable $e) {
            Log::warning('GenerateSubtitlesJob: whisper failed', [
                'media_id' => $media->id,
                'locale'   => $this->locale,
                'error'    => $e->getMessage(),
            ]);

            return;
        }

        $path = "subtitles/activity-{$media->activity_id}-{$this->locale}.vtt";
        Storage::disk('local')->put($path, $vtt);

        ActivityMedia::updateOrCreate(
            [
                'activity_id' => $media->activity_id,
                'modality'    => "subtitle:{$this->locale}",
            ],
            [
                'media_type' => 'subtitle',
                'url'        => Storage::disk('local')->url($path),
                'label'      => "Subtitles ({$this->locale})",
                'is_primary' => false,
            ],
        );
    }
}
