<?php

namespace Tests\Feature\Institutional;

use App\Models\SchoolAdminInvite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class InviteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function valid_invite_token_resolves_and_shows_signup_form(): void
    {
        $invite = SchoolAdminInvite::create([
            'email' => 'principal@school.example',
            'school_name' => 'Greenwood Primary',
            'seats' => 25,
            'invite_token' => Str::random(48),
            'expires_at' => now()->addDays(7),
        ]);

        $response = $this->get(route('institutional.invite.show', ['token' => $invite->invite_token]));

        $response->assertStatus(200);
        $response->assertSee('Greenwood Primary');
        $response->assertSee('principal@school.example');
    }

    /** @test */
    public function expired_invite_token_is_rejected(): void
    {
        $invite = SchoolAdminInvite::create([
            'email' => 'late@school.example',
            'school_name' => 'Sunset Academy',
            'seats' => 10,
            'invite_token' => Str::random(48),
            'expires_at' => now()->subDay(),
        ]);

        $response = $this->get(route('institutional.invite.show', ['token' => $invite->invite_token]));
        $response->assertStatus(410);
    }

    /** @test */
    public function school_admin_can_view_dashboard_with_seat_summary(): void
    {
        $admin = User::factory()->create(['role' => 'school_admin']);
        SchoolAdminInvite::create([
            'email' => $admin->email,
            'school_name' => 'Riverside School',
            'seats' => 40,
            'invite_token' => Str::random(48),
            'expires_at' => now()->addMonth(),
            'accepted_at' => now(),
            'accepted_by_user_id' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->get(route('school.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Riverside School');
        $response->assertSee('40');
    }

    /** @test */
    public function accepting_invite_creates_school_admin_user_and_marks_accepted(): void
    {
        $invite = SchoolAdminInvite::create([
            'email' => 'newadmin@school.example',
            'school_name' => 'Maple Heights',
            'seats' => 50,
            'invite_token' => Str::random(48),
            'expires_at' => now()->addDays(7),
        ]);

        $response = $this->post(route('institutional.invite.accept', ['token' => $invite->invite_token]), [
            'name' => 'New Admin',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ]);

        $response->assertRedirect(route('school.dashboard'));

        $user = User::where('email', 'newadmin@school.example')->first();
        $this->assertNotNull($user);
        $this->assertSame('school_admin', $user->role);

        $invite->refresh();
        $this->assertNotNull($invite->accepted_at);
        $this->assertSame($user->id, $invite->accepted_by_user_id);
    }
}
