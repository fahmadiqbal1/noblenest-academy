<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PublicMetadataTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function home_page_exposes_route_specific_metadata(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('<title>NobleNest Global Academy | Family-First Learning Platform</title>', false);
        $response->assertSee('content="Explore NobleNest Global Academy: a family-first learning platform with adaptive courses, onboarding guidance, and AI support for parents, students, teachers, and admins."', false);
        $response->assertSee('content="'.asset('og-home.png').'"', false);
    }

    #[Test]
    public function auth_pages_expose_route_specific_metadata(): void
    {
        $loginResponse = $this->get('/login');
        $registerResponse = $this->get('/register');

        $loginResponse->assertOk();
        $loginResponse->assertSee('<title>Login | NobleNest Global Academy</title>', false);
        $loginResponse->assertSee('content="'.asset('og-login.png').'"', false);

        $registerResponse->assertOk();
        $registerResponse->assertSee('<title>Create Your Account | NobleNest Global Academy</title>', false);
        $registerResponse->assertSee('content="'.asset('og-register.png').'"', false);
    }

    #[Test]
    public function generated_social_preview_assets_exist(): void
    {
        $this->assertFileExists(public_path('og-home.png'));
        $this->assertFileExists(public_path('og-login.png'));
        $this->assertFileExists(public_path('og-register.png'));
        $this->assertFileExists(public_path('og-image.png'));
        $this->assertFileExists(public_path('social-preview.png'));
    }
}
