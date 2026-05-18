<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ChildProfile;
use App\Models\User;
use Database\Seeders\DemoChildrenSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Definition-of-Done #5: `migrate:fresh --seed` must yield a demo usable
 * by every role. DemoChildrenSeeder existed but was wired into NO
 * DatabaseSeeder block, so the demo parent had zero children and the
 * child dashboard / activity-player journey could not be exercised.
 */
class DemoSeedUsableTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function demo_seed_produces_a_parent_with_children(): void
    {
        $this->seed(DemoChildrenSeeder::class);

        $parent = User::where('role', 'Parent')
            ->whereHas('childProfiles')
            ->first();

        $this->assertNotNull($parent, 'demo must include a parent with children');
        $this->assertGreaterThan(0, ChildProfile::where('parent_id', $parent->id)->count());
    }
}
