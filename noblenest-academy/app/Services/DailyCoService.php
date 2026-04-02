<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * DailyCoService — wraps the Daily.co REST API for classroom room management.
 *
 * Requires DAILY_CO_API_KEY in .env.
 * Falls back gracefully when the key is not configured, returning null.
 */
class DailyCoService
{
    private const BASE_URL = 'https://api.daily.co/v1';

    private ?string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.daily_co.api_key');
    }

    /** Returns true if Daily.co is configured */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Create a Daily.co room for a class session.
     * Returns ['room_url' => ..., 'room_name' => ...] on success, null on failure.
     */
    public function createRoom(int $sessionId, int $durationMinutes = 60, int $maxParticipants = 20): ?array
    {
        if (!$this->isConfigured()) {
            return null;
        }

        $roomName = 'noblenest-session-' . $sessionId . '-' . Str::random(6);

        $expiryTimestamp = now()->addMinutes($durationMinutes + 30)->timestamp;

        $payload = [
            'name'       => $roomName,
            'properties' => [
                'max_participants' => $maxParticipants,
                'exp'              => $expiryTimestamp,
                'eject_at_room_exp'=> true,
                'enable_chat'      => true,
                'enable_screenshare' => true,
                'start_audio_off'  => true,
                'start_video_off'  => false,
            ],
        ];

        try {
            $response = Http::withToken($this->apiKey)
                ->acceptJson()
                ->timeout(15)
                ->post(self::BASE_URL . '/rooms', $payload);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'room_url'        => $data['url'],
                    'daily_room_name' => $data['name'],
                ];
            }
        } catch (\Throwable) {
            // Non-critical — classroom will fall back to peer-to-peer
        }

        return null;
    }

    /**
     * Delete a Daily.co room by name.
     */
    public function deleteRoom(string $roomName): void
    {
        if (!$this->isConfigured()) {
            return;
        }

        try {
            Http::withToken($this->apiKey)
                ->timeout(10)
                ->delete(self::BASE_URL . "/rooms/{$roomName}");
        } catch (\Throwable) {
            // Non-critical
        }
    }

    /**
     * Create a meeting token (pre-auth) for a participant.
     */
    public function createMeetingToken(string $roomName, string $userName, bool $isOwner = false): ?string
    {
        if (!$this->isConfigured()) {
            return null;
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->acceptJson()
                ->timeout(10)
                ->post(self::BASE_URL . '/meeting-tokens', [
                    'properties' => [
                        'room_name'  => $roomName,
                        'user_name'  => $userName,
                        'is_owner'   => $isOwner,
                        'exp'        => now()->addHours(3)->timestamp,
                    ],
                ]);

            if ($response->successful()) {
                return $response->json('token');
            }
        } catch (\Throwable) {
            // Fall through
        }

        return null;
    }
}
