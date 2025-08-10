<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function message(Request $request)
    {
        $data = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userMsg = trim($data['message']);
        $provider = env('AI_ASSIST_PROVIDER', 'mock');

        // For now, we keep it mock. You can integrate a real provider later via env config.
        $reply = $this->mockReply($userMsg);

        \Log::info('AI assistant request', [
            'user_id' => optional(auth()->user())->id,
            'len' => mb_strlen($userMsg),
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'reply' => $reply,
            'provider' => $provider,
            'suggestions' => [
                'Show me a weekly plan for a 3 year old',
                'Recommend activities for language skills',
                'How do I get started with STEM for age 8?',
            ],
        ]);
    }

    protected function mockReply(string $msg): string
    {
        // Friendly, age-appropriate, safe response
        $base = "Hello! I'm your Noble Nest assistant. I can suggest activities, courses, and a simple weekly plan. ";
        if ($msg !== '') {
            $base .= "You said: '" . mb_substr($msg, 0, 200) . "'. ";
        }
        $base .= "Try telling me your child's age and preferred learning language to get a tailored plan.";
        return $base;
    }
}
