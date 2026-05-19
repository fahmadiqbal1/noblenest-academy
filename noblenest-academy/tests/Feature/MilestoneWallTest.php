<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * M1: production /milestones 500'd because the controller queried a
 * non-existent `child_achievements` table. Guard: the public page must
 * render without throwing while that persistence layer is not built.
 */
class MilestoneWallTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function public_milestones_wall_renders_without_500(): void
    {
        $this->get('/milestones')->assertOk();
    }
}
