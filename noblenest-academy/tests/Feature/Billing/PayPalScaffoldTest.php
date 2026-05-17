<?php

namespace Tests\Feature\Billing;

use App\Services\PayPalCheckoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayPalScaffoldTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'services.paypal.client_id'  => null,
            'services.paypal.secret'     => null,
            'services.paypal.webhook_id' => null,
            'services.paypal.env'        => 'sandbox',
        ]);
    }

    /** @test */
    public function create_order_returns_stub_when_credentials_are_empty(): void
    {
        $svc = new PayPalCheckoutService();
        $this->assertTrue($svc->isStubMode());

        $order = $svc->createOrder(25.00, 'USD');

        $this->assertArrayHasKey('id', $order);
        $this->assertTrue($order['stub']);
        $this->assertSame('CREATED', $order['status']);
        $this->assertSame('25.00', $order['amount']);
    }

    /** @test */
    public function capture_order_returns_stub_when_credentials_are_empty(): void
    {
        $svc = new PayPalCheckoutService();
        $result = $svc->captureOrder('STUB-ABC123');

        $this->assertSame('STUB-ABC123', $result['id']);
        $this->assertSame('COMPLETED', $result['status']);
        $this->assertTrue($result['stub']);
    }

    /** @test */
    public function verify_webhook_returns_true_in_stub_mode(): void
    {
        $svc = new PayPalCheckoutService();
        $this->assertTrue($svc->verifyWebhook('{}', []));
    }
}
