<?php

namespace App\Services\Providers;

/**
 * Phase 6 — lifecycle status for an avatar-video generation job.
 */
enum VideoGenerationStatus: string
{
    case Queued     = 'queued';
    case Processing = 'processing';
    case Completed  = 'completed';
    case Failed     = 'failed';
}
