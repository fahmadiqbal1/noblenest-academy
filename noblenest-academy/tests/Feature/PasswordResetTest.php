<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function forgot_password_page_loads(): void
    {
        $this->get('/forgot-password')->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function reset_link_request_requires_email(): void
    {
        $this->post('/forgot-password', [])
             ->assertSessionHasErrors('email');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function reset_link_request_returns_success_for_existing_email(): void
    {
        User::factory()->create(['email' => 'real@example.com']);

        $this->post('/forgot-password', ['email' => 'real@example.com'])
             ->assertSessionHas('status');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function reset_link_request_returns_success_for_nonexistent_email(): void
    {
        // Anti-enumeration: same response for unknown emails
        $this->post('/forgot-password', ['email' => 'nobody@example.com'])
             ->assertSessionHas('status');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function reset_link_creates_token_in_database(): void
    {
        User::factory()->create(['email' => 'user@example.com']);

        $this->post('/forgot-password', ['email' => 'user@example.com']);

        $this->assertDatabaseHas('password_reset_tokens', ['email' => 'user@example.com']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function reset_password_page_loads(): void
    {
        $this->get('/reset-password/abc')
             ->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function password_can_be_reset_with_valid_token(): void
    {
        $user = User::factory()->create(['email' => 'user@example.com']);
        $token = 'valid-token-string-for-testing-64chars-padded-to-be-long-enough!';

        DB::table('password_reset_tokens')->insert([
            'email'      => 'user@example.com',
            'token'      => Hash::make($token),
            'created_at' => now(),
        ]);

        $this->post('/reset-password', [
            'token'                 => $token,
            'email'                 => 'user@example.com',
            'password'              => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])->assertRedirect(route('login'));

        // Token should be deleted after use
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => 'user@example.com']);

        // New password should work
        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function password_reset_fails_with_invalid_token(): void
    {
        User::factory()->create(['email' => 'user@example.com']);

        DB::table('password_reset_tokens')->insert([
            'email'      => 'user@example.com',
            'token'      => Hash::make('real-token'),
            'created_at' => now(),
        ]);

        $this->post('/reset-password', [
            'token'                 => 'wrong-token',
            'email'                 => 'user@example.com',
            'password'              => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])->assertSessionHasErrors('email');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function password_reset_fails_with_expired_token(): void
    {
        User::factory()->create(['email' => 'user@example.com']);
        $token = 'expired-token-value-padded-to-be-long-enough-for-the-test-here!';

        DB::table('password_reset_tokens')->insert([
            'email'      => 'user@example.com',
            'token'      => Hash::make($token),
            'created_at' => now()->subHours(2), // Expired (>60 min)
        ]);

        $this->post('/reset-password', [
            'token'                 => $token,
            'email'                 => 'user@example.com',
            'password'              => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])->assertSessionHasErrors('email');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function password_reset_requires_confirmation(): void
    {
        $this->post('/reset-password', [
            'token'    => 'some-token',
            'email'    => 'user@example.com',
            'password' => 'newpassword123',
        ])->assertSessionHasErrors('password');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function password_reset_enforces_minimum_length(): void
    {
        $this->post('/reset-password', [
            'token'                 => 'some-token',
            'email'                 => 'user@example.com',
            'password'              => 'short',
            'password_confirmation' => 'short',
        ])->assertSessionHasErrors('password');
    }
}
