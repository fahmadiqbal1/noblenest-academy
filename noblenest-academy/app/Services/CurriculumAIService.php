<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Curriculum AI Service
 *
 * Bridge between Laravel and the Python curriculum-ai sidecar.
 * Generates activities with full Phase 2 metadata by calling the Python service.
 *
 * The Python service validates all output against activity_payload.schema.json.
 */
class CurriculumAIService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected int $timeout = 60;

    public function __construct()
    {
        $this->baseUrl = config('services.curriculum_ai.base_url') ?? 'http://localhost:8001';
        $this->apiKey = config('services.curriculum_ai.api_key') ?? '';
    }

    /**
     * Generate one or more activities with full Phase 2 metadata.
     *
     * @param string $subject Activity subject (math, language, science, art, etc.)
     * @param string $ageGroup Age tier (baby, toddler, preschool, school)
     * @param string $language Language code (english, french, spanish, etc.)
     * @param bool $isFree Whether to generate free-tier activities
     * @param int $count Number of activities to generate
     * @param array $targetCognitiveDomains Optional domains to prioritize
     *
     * @return array {
     *     activities: [ActivityPayload[], ...],
     *     model: string,
     *     count: int,
     *     validation: 'pydantic'
     * }
     *
     * @throws \Exception If generation fails
     */
    public function generateActivities(
        string $subject,
        string $ageGroup,
        string $language,
        bool $isFree = false,
        int $count = 1,
        array $targetCognitiveDomains = []
    ): array {
        $payload = [
            'subject'                  => $subject,
            'age_group'                => $ageGroup,
            'language'                 => $language,
            'is_free'                  => $isFree,
            'count'                    => $count,
            'target_cognitive_domains' => $targetCognitiveDomains,
        ];

        try {
            Log::info('Calling curriculum-ai sidecar', [
                'subject'   => $subject,
                'age_group' => $ageGroup,
                'language'  => $language,
                'count'     => $count,
            ]);

            $response = $this->client()
                ->post("{$this->baseUrl}/api/activities/generate", $payload)
                ->throw()
                ->json();

            // Validate response structure
            if (!isset($response['activities']) || !is_array($response['activities'])) {
                throw new \Exception('Invalid response structure from curriculum-ai sidecar: missing activities array');
            }

            Log::info('Successfully generated activities', [
                'count'      => count($response['activities']),
                'validation' => $response['validation'] ?? 'unknown',
            ]);

            return $response;
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error('Curriculum AI sidecar error', [
                'status'  => $e->response->status(),
                'message' => $e->getMessage(),
                'body'    => $e->response->body(),
            ]);

            throw new \Exception(
                "Curriculum generation failed: {$e->getMessage()}",
                previous: $e
            );
        } catch (\Exception $e) {
            Log::error('Curriculum AI error', [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Health check: verify the Python sidecar is running.
     */
    public function healthCheck(): bool
    {
        try {
            $response = $this->client()
                ->timeout(5)
                ->get("{$this->baseUrl}/health")
                ->json();

            return ($response['status'] ?? null) === 'healthy';
        } catch (\Exception $e) {
            Log::warning('Curriculum AI health check failed', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get the HTTP client with proper configuration.
     */
    protected function client(): PendingRequest
    {
        return Http::timeout($this->timeout)
            ->withHeaders([
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
                'User-Agent'    => 'NoblaNestAcademy/1.0',
            ])
            ->when($this->apiKey, fn ($client) => $client->withHeader('Authorization', "Bearer {$this->apiKey}"));
    }
}
