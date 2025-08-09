<?php

namespace Tests\Feature;

use Tests\TestCase;

class SmokeTest extends TestCase
{
    /** @test */
    public function home_page_loads()
    {
        $resp = $this->get('/');
        $resp->assertStatus(200);
    }
}
