<?php

namespace Database\Seeders;

use App\Models\AIJob;
use App\Models\AIProviderConfig;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoOrchestratorSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'Admin')->first();

        // Demo AI providers — Grok (xAI) + Anthropic only
        $providers = [
            [
                'name'               => 'Anthropic Claude',
                'slug'               => 'anthropic-claude',
                'model'              => 'claude-haiku-4-5-20251001',
                'is_active'          => true,
                'connection_status'  => 'live',
                'connection_message' => 'Successfully connected. Latency: 420ms.',
                'capabilities'       => ['text', 'quiz'],
                'extra_config'       => ['driver' => 'anthropic'],
                'last_checked_at'    => now()->subMinutes(5),
                'last_live_at'       => now()->subMinutes(5),
            ],
            [
                'name'               => 'Grok (xAI)',
                'slug'               => 'grok-xai',
                'model'              => 'grok-beta',
                'is_active'          => true,
                'connection_status'  => 'configured',
                'connection_message' => 'API key configured — click Verify to test connection.',
                'capabilities'       => ['text', 'quiz'],
                'extra_config'       => ['driver' => 'grok'],
                'last_checked_at'    => null,
                'last_live_at'       => null,
            ],
        ];

        foreach ($providers as $p) {
            AIProviderConfig::firstOrCreate(
                ['slug' => $p['slug']],
                $p
            );
        }

        // Remove legacy providers that are no longer supported
        AIProviderConfig::whereIn('slug', ['openai-gpt4o', 'stability-ai', 'elevenlabs'])->delete();

        // Demo jobs — only create if the table is empty
        if (AIJob::count() > 0) {
            return;
        }

        $jobs = [
            [
                'type'               => 'activity_content',
                'status'             => 'completed',
                'provider'           => 'anthropic-claude',
                'locale'             => 'en',
                'user_id'            => $admin?->id,
                'moderation_status'  => 'approved',
                'payload'            => ['prompt' => 'Create a 15-minute number tracing activity for ages 3–4 using dotted outlines and counting rhymes.'],
                'result'             => ['type' => 'text', 'content' => "Activity: Number Tracing Adventure\n\nMaterials: Dotted number cards 1–5, crayons\n\nSteps:\n1. Warm up — count fingers together (2 min)\n2. Trace number 1 while singing 'One little duck' (3 min)\n3. Trace numbers 2–5 with animal sound prompts (8 min)\n4. Free draw your favourite number (2 min)\n\nLearning goals: fine motor, numeracy, phonological awareness."],
                'started_at'         => now()->subHours(3),
                'completed_at'       => now()->subHours(3)->addSeconds(18),
                'created_at'         => now()->subHours(3),
                'updated_at'         => now()->subHours(3)->addSeconds(18),
            ],
            [
                'type'               => 'quiz',
                'status'             => 'completed',
                'provider'           => 'grok-xai',
                'locale'             => 'en',
                'user_id'            => $admin?->id,
                'moderation_status'  => 'pending',
                'payload'            => ['prompt' => 'Generate 5 quiz questions about farm animals for children aged 4–6.'],
                'result'             => ['type' => 'text', 'content' => "Q1: Which animal says \"moo\"?\nA) Dog  B) Cow ✓  C) Sheep\n\nQ2: Who lays eggs on the farm?\nA) Hen ✓  B) Horse  C) Pig\n\nQ3: Which animal has a long mane?\nA) Donkey  B) Goat  C) Horse ✓\n\nQ4: What do you call a baby cow?\nA) Foal  B) Calf ✓  C) Lamb\n\nQ5: Which animal says \"oink\"?\nA) Pig ✓  B) Duck  C) Cat"],
                'started_at'         => now()->subHours(1),
                'completed_at'       => now()->subHours(1)->addSeconds(12),
                'created_at'         => now()->subHours(1),
                'updated_at'         => now()->subHours(1)->addSeconds(12),
            ],
            [
                'type'               => 'activity_content',
                'status'             => 'completed',
                'provider'           => 'anthropic-claude',
                'locale'             => 'ar',
                'user_id'            => $admin?->id,
                'moderation_status'  => 'rejected',
                'payload'            => ['prompt' => 'أنشئ نشاطاً للرسم للأطفال من 5-7 سنوات بموضوع الطبيعة.'],
                'result'             => ['type' => 'text', 'content' => 'نشاط: رسم المناظر الطبيعية\n\nالمواد: أقلام تلوين، ورق أبيض\n\nالخطوات:\n1. ارسم السماء الزرقاء في الجزء العلوي\n2. أضف الشمس والغيوم\n3. ارسم الأشجار والزهور'],
                'error_message'      => null,
                'started_at'         => now()->subHours(5),
                'completed_at'       => now()->subHours(5)->addSeconds(22),
                'created_at'         => now()->subHours(5),
                'updated_at'         => now()->subHours(4)->addMinutes(45),
            ],
            [
                'type'               => 'activity_content',
                'status'             => 'queued',
                'provider'           => 'grok-xai',
                'locale'             => 'ur',
                'user_id'            => $admin?->id,
                'moderation_status'  => 'pending',
                'payload'            => ['prompt' => 'اردو میں 4-6 سال کے بچوں کے لیے رنگوں پر 5 سوالات بنائیں۔'],
                'result'             => null,
                'error_message'      => null,
                'started_at'         => null,
                'completed_at'       => null,
                'created_at'         => now()->subMinutes(3),
                'updated_at'         => now()->subMinutes(3),
            ],
        ];

        foreach ($jobs as $job) {
            AIJob::create($job);
        }
    }
}
