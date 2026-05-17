<?php

namespace Tests\Feature\Billing;

use App\Models\PricingTier;
use App\Services\PricingService;
use Database\Seeders\PricingTierSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class PricingPPPTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PricingTierSeeder::class);
    }

    /** @test */
    public function country_override_returns_lower_ppp_price(): void
    {
        /** @var PricingService $svc */
        $svc = app(PricingService::class);

        $base = $svc->resolveTier('family', null);
        $india = $svc->resolveTier('family', 'IN');

        $this->assertInstanceOf(PricingTier::class, $base);
        $this->assertInstanceOf(PricingTier::class, $india);
        $this->assertSame('family', $india->key);
        $this->assertSame('IN', $india->country_code);
        $this->assertLessThan((float) $base->price_monthly, (float) $india->price_monthly);
        $this->assertEqualsWithDelta(25.00 * 0.30, (float) $india->price_monthly, 0.01);
    }

    /** @test */
    public function unknown_country_falls_back_to_base_global_tier(): void
    {
        /** @var PricingService $svc */
        $svc = app(PricingService::class);

        $tier = $svc->resolveTier('individual', 'ZZ');

        $this->assertNotNull($tier);
        $this->assertSame('individual', $tier->key);
        $this->assertNull($tier->country_code);
        $this->assertEqualsWithDelta(12.00, (float) $tier->price_monthly, 0.01);
    }

    /** @test */
    public function country_resolution_reads_cloudflare_header(): void
    {
        /** @var PricingService $svc */
        $svc = app(PricingService::class);

        $req = Request::create('/pricing');
        $req->headers->set('CF-IPCountry', 'pk');
        // Bind a session so cache logic doesn't blow up.
        $req->setLaravelSession($this->app['session.store']);

        $country = $svc->resolveCountryFromRequest($req);

        $this->assertSame('PK', $country);
    }
}
