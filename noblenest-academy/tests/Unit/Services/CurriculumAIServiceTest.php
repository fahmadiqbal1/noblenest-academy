<?php

namespace Tests\Unit\Services;

use App\Services\CurriculumAIService;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\TestCase;

class CurriculumAIServiceTest extends TestCase
{
    protected CurriculumAIService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CurriculumAIService();
    }

    /**
     * Test successful activity generation with Phase 2 metadata.
     */
    public function test_generate_activities_success()
    {
        Http::fake([
            'localhost:8001/api/activities/generate' => Http::response([
                'activities' => [
                    [
                        'title' => 'Counting Stars',
                        'description' => 'Count objects in the sky.',
                        'instructions' => 'Step 1: Look up. Step 2: Count.',
                        'materials' => ['blanket', 'night sky'],
                        'duration_minutes' => 15,
                        'difficulty' => 'easy',
                        'age_tier' => 'baby',
                        'subject' => 'math',
                        'language' => 'english',
                        'is_free' => true,
                        'mess_level' => 'low',
                        'safety_warnings' => [],
                        'adaptations' => [
                            'easier' => 'Use 1-3 objects',
                            'harder' => 'Count higher',
                        ],
                        'cognitive_domain' => 'math',
                        'developmental_domains' => ['cognitive', 'language'],
                        'materials_cost' => 0,
                        'parent_involvement' => 'moderate',
                        'instructions_for_parent' => 'Encourage counting and repetition.',
                    ],
                ],
                'model' => 'claude-sonnet-4-6',
                'count' => 1,
                'validation' => 'pydantic',
            ], 200),
        ]);

        $result = $this->service->generateActivities('math', 'baby', 'english');

        $this->assertArrayHasKey('activities', $result);
        $this->assertCount(1, $result['activities']);
        $this->assertEquals('Counting Stars', $result['activities'][0]['title']);
        $this->assertEquals('math', $result['activities'][0]['cognitive_domain']);
        $this->assertEquals('low', $result['activities'][0]['mess_level']);
    }

    /**
     * Test error handling when sidecar is unreachable.
     */
    public function test_generate_activities_connection_error()
    {
        Http::fake([
            'localhost:8001/api/activities/generate' => Http::response([], 500),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Curriculum generation failed');

        $this->service->generateActivities('math', 'baby', 'english');
    }

    /**
     * Test invalid response structure handling.
     */
    public function test_generate_activities_invalid_response()
    {
        Http::fake([
            'localhost:8001/api/activities/generate' => Http::response(
                ['error' => 'Something went wrong'],
                200
            ),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid response structure');

        $this->service->generateActivities('math', 'baby', 'english');
    }

    /**
     * Test health check.
     */
    public function test_health_check_success()
    {
        Http::fake([
            'localhost:8001/health' => Http::response(['status' => 'healthy'], 200),
        ]);

        $result = $this->service->healthCheck();

        $this->assertTrue($result);
    }

    /**
     * Test health check failure.
     */
    public function test_health_check_failure()
    {
        Http::fake([
            'localhost:8001/health' => Http::response(['status' => 'unhealthy'], 200),
        ]);

        $result = $this->service->healthCheck();

        $this->assertFalse($result);
    }
}
