<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class ParentPinTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function pin_is_set_via_onboarding_step2(): void
    {
        $parent = User::factory()->create(['role' => 'Parent']);

        $this->actingAs($parent)->post('/onboarding/step/2', [
            'name'       => 'Parent',
            'parent_pin' => '4321',
        ]);

        $this->assertTrue(Hash::check('4321', $parent->fresh()->parent_pin_hash));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function pin_gate_redirects_when_unverified(): void
    {
        $parent = User::factory()->create([
            'role'            => 'Parent',
            'parent_pin_hash' => Hash::make('5678'),
        ]);

        $this->actingAs($parent)
            ->get('/privacy/export')
            ->assertRedirect(route('parent.pin.show'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function correct_pin_grants_access(): void
    {
        $parent = User::factory()->create([
            'role'            => 'Parent',
            'parent_pin_hash' => Hash::make('5678'),
        ]);

        RateLimiter::clear('parent-pin:' . $parent->id);

        $this->actingAs($parent)
            ->post('/parent/pin', ['pin' => '5678'])
            ->assertRedirect();

        $this->assertNotNull(session('parent_pin_verified_at'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function wrong_pin_returns_error(): void
    {
        $parent = User::factory()->create([
            'role'            => 'Parent',
            'parent_pin_hash' => Hash::make('5678'),
        ]);

        RateLimiter::clear('parent-pin:' . $parent->id);

        $this->actingAs($parent)
            ->from(route('parent.pin.show'))
            ->post('/parent/pin', ['pin' => '0000'])
            ->assertSessionHasErrors('pin');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function pin_locks_out_after_three_failures(): void
    {
        $parent = User::factory()->create([
            'role'            => 'Parent',
            'parent_pin_hash' => Hash::make('5678'),
        ]);

        RateLimiter::clear('parent-pin:' . $parent->id);

        for ($i = 0; $i < 3; $i++) {
            $this->actingAs($parent)
                ->from(route('parent.pin.show'))
                ->post('/parent/pin', ['pin' => '0000']);
        }

        // 4th attempt — locked out (RateLimiter::tooManyAttempts triggers).
        $response = $this->actingAs($parent)
            ->from(route('parent.pin.show'))
            ->post('/parent/pin', ['pin' => '5678']);

        $response->assertSessionHasErrors('pin');
    }
}
