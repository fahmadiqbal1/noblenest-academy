<?php

namespace App\Http\Controllers;

use App\Services\AIAssistantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function __construct(protected AIAssistantService $aiService)
    {
    }

    public function message(Request $request)
    {
        $data = $request->validate([
            'message'          => 'required|string|max:1000',
            'child_profile_id' => 'nullable|integer|exists:child_profiles,id',
        ]);

        $userMsg = trim($data['message']);
        $context = $this->buildContext($request, $data);

        // Get AI response with content filtering and safety checks
        $response = $this->aiService->chat($userMsg, $context);

        Log::info('AI assistant request', [
            'user_id'  => Auth::id(),
            'msg_len'  => mb_strlen($userMsg),
            'provider' => $response['provider'] ?? 'unknown',
            'ip'       => $request->ip(),
        ]);

        return response()->json([
            'reply'       => $response['reply'],
            'provider'    => $response['provider'],
            'model'       => $response['model'] ?? null,
            'suggestions' => $response['suggestions'],
        ]);
    }

    /**
     * Build context array from request and user data.
     */
    protected function buildContext(Request $request, array $data): array
    {
        $context = [];
        $user = Auth::user();

        // Get child profile context if specified
        if (!empty($data['child_profile_id']) && $user) {
            /** @var \App\Models\User $user */
            $childProfile = $user->childProfiles()
                ->find($data['child_profile_id']);

            if ($childProfile) {
                $context['child_age'] = $childProfile->age_months;
                $context['language'] = $childProfile->preferred_language;
                $context['interests'] = $childProfile->learning_goals ?? [];
            }
        }

        // Fallback to session/user language
        if (empty($context['language'])) {
            $context['language'] = session('lang', $user?->preferred_language ?? 'en');
        }

        return $context;
    }

    /**
     * Check AI service health status.
     */
    public function status()
    {
        return response()->json([
            'available' => $this->aiService->isAvailable(),
            'provider'  => $this->aiService->getProviderName(),
        ]);
    }
}
