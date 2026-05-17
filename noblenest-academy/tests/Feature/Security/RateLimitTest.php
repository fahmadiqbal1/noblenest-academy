<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

/**
 * Phase 8 — verifies named rate limiters reject excess traffic with 429.
 */
class RateLimitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Wipe any previously-accrued hits so each test starts clean.
        foreach (['auth', 'register', 'ai-assistant'] as $key) {
            // Clear by common signatures used in this test
            RateLimiter::clear($key);
        }
    }

    public function test_sixth_login_attempt_within_a_minute_is_rate_limited(): void
    {
        User::factory()->create([
            'email' => 'victim@example.com',
            'password' => Hash::make('correct-password'),
        ]);

        $payload = ['email' => 'victim@example.com', 'password' => 'wrong-password'];

        $last = null;
        for ($i = 1; $i <= 6; $i++) {
            $last = $this->post('/login', $payload);
            if ($last->status() === 429) {
                break;
            }
        }

        $this->assertSame(429, $last->status(), 'Expected 429 after 5 failed login attempts within a minute.');
    }

    public function test_fourth_register_attempt_within_a_minute_is_rate_limited(): void
    {
        $last = null;
        for ($i = 1; $i <= 4; $i++) {
            $last = $this->post('/register', [
                'name' => "User {$i}",
                'email' => "user{$i}@example.com",
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'Parent',
            ]);
            if ($last->status() === 429) {
                break;
            }
        }

        $this->assertSame(429, $last->status(), 'Expected 429 after 3 register attempts within a minute.');
    }

    public function test_thirty_first_ai_assistant_call_is_rate_limited(): void
    {
        $user = User::factory()->create(['role' => 'Parent']);
        $this->actingAs($user);

        $last = null;
        for ($i = 1; $i <= 31; $i++) {
            $last = $this->post('/ai/assistant/message', ['message' => 'hi']);
            if ($last->status() === 429) {
                break;
            }
        }

        $this->assertSame(429, $last->status(), 'Expected 429 after 30 AI assistant requests within a minute.');
    }
}
