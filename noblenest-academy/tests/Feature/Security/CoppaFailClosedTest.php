<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Models\ChildProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Regression guard for the security tranche:
 *  - S1: RequireParentalConsent must FAIL CLOSED when a child's age cannot
 *        be determined (no DOB) — previously it granted access (COPPA hole).
 *  - S2: RequireParentPin must not bypass for a parent with no PIN; it must
 *        redirect to set one, and verify() must set it on first use.
 */
class CoppaFailClosedTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function unknown_age_child_requires_consent(): void
    {
        $parent = User::factory()->create(['role' => 'Parent']);
        $child = ChildProfile::factory()->create([
            'parent_id' => $parent->id,
            'date_of_birth' => null,
            'parental_consent_at' => null,
        ]);

        $this->actingAs($parent)
            ->get("/child/{$child->id}/activities")
            ->assertRedirect(route('privacy.parental-consent', ['child' => $child->id]));
    }

    #[Test]
    public function unknown_age_child_with_consent_is_allowed(): void
    {
        $parent = User::factory()->create(['role' => 'Parent']);
        $child = ChildProfile::factory()->create([
            'parent_id' => $parent->id,
            'date_of_birth' => null,
            'parental_consent_at' => now(),
        ]);

        $this->actingAs($parent)
            ->get("/child/{$child->id}/activities")
            ->assertOk();
    }

    #[Test]
    public function pin_gate_does_not_bypass_when_no_pin_set(): void
    {
        $parent = User::factory()->create([
            'role' => 'Parent',
            'parent_pin_hash' => null,
        ]);

        $this->actingAs($parent)
            ->get('/privacy/export')
            ->assertRedirect(route('parent.pin.show'));
    }

    #[Test]
    public function first_pin_submission_sets_it_and_grants_access(): void
    {
        $parent = User::factory()->create([
            'role' => 'Parent',
            'parent_pin_hash' => null,
        ]);

        $this->actingAs($parent)
            ->post('/parent/pin', ['pin' => '2468'])
            ->assertRedirect();

        $this->assertTrue(Hash::check('2468', $parent->fresh()->parent_pin_hash));
        $this->assertNotNull(session('parent_pin_verified_at'));
    }
}
