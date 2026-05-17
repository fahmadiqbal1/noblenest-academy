<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ChildProfile;
use App\Models\ConsentReceipt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Phase 5 — 5-step onboarding flow.
 */
class OnboardingFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $parent;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parent = User::factory()->create(['role' => 'Parent']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function legacy_onboarding_redirects_to_step1(): void
    {
        $this->actingAs($this->parent)
            ->get('/onboarding')
            ->assertRedirect(route('onboarding.step1'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function step1_loads_and_persists_language(): void
    {
        $this->actingAs($this->parent)->get('/onboarding/step/1')->assertOk();

        $this->actingAs($this->parent)
            ->post('/onboarding/step/1', ['preferred_language' => 'ar'])
            ->assertRedirect(route('onboarding.step2'));

        $this->assertSame('ar', $this->parent->fresh()->preferred_language);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function step2_sets_name_and_hashes_pin(): void
    {
        $this->actingAs($this->parent)
            ->post('/onboarding/step/2', [
                'name'         => 'Aisha Parent',
                'country_code' => 'pk',
                'parent_pin'   => '1234',
            ])
            ->assertRedirect(route('onboarding.step3'));

        $fresh = $this->parent->fresh();
        $this->assertSame('Aisha Parent', $fresh->name);
        $this->assertSame('PK', $fresh->country_code);
        $this->assertNotEmpty($fresh->parent_pin_hash);
        $this->assertTrue(Hash::check('1234', $fresh->parent_pin_hash));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function step3_records_consent_intent_in_session(): void
    {
        $this->actingAs($this->parent)
            ->post('/onboarding/step/3', ['agree' => 1])
            ->assertRedirect(route('onboarding.step4'))
            ->assertSessionHas('onboarding.consent_signed_at');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function step4_creates_child_and_persists_consent_receipt(): void
    {
        // Pre-stage step3 consent.
        $this->actingAs($this->parent)
            ->post('/onboarding/step/3', ['agree' => 1]);

        $response = $this->actingAs($this->parent)
            ->post('/onboarding/step/4', [
                'child_name'    => 'Little One',
                'date_of_birth' => now()->subYears(4)->format('Y-m-d'),
                'gender'        => 'female',
            ]);

        $child = ChildProfile::where('name', 'Little One')->firstOrFail();

        $response->assertRedirect(route('onboarding.step5', ['child' => $child->id]));
        $this->assertNotNull($child->parental_consent_at);

        $receipt = ConsentReceipt::where('parent_user_id', $this->parent->id)
            ->where('child_profile_id', $child->id)
            ->first();
        $this->assertNotNull($receipt);
        $this->assertSame('2026-05', $receipt->document_version);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function complete_flow_marks_user_onboarded(): void
    {
        $this->actingAs($this->parent)->post('/onboarding/step/1', ['preferred_language' => 'en']);
        $this->actingAs($this->parent)->post('/onboarding/step/2', [
            'name' => 'Parent', 'parent_pin' => '4321',
        ]);
        $this->actingAs($this->parent)->post('/onboarding/step/3', ['agree' => 1]);
        $this->actingAs($this->parent)->post('/onboarding/step/4', [
            'child_name'    => 'Kid',
            'date_of_birth' => now()->subYears(3)->format('Y-m-d'),
        ]);

        $child = ChildProfile::where('parent_id', $this->parent->id)->firstOrFail();

        $this->actingAs($this->parent)
            ->post("/onboarding/step/5/{$child->id}")
            ->assertRedirect();

        $this->assertTrue((bool) $this->parent->fresh()->is_onboarded);
    }
}
