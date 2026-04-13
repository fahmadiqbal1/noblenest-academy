<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | Toggle product features on or off. Set these in your .env file.
    | Features default to false (disabled) until explicitly enabled.
    |
    */

    // Core modules
    'maternal_module'         => (bool) env('MATERNAL_MODULE_ENABLED', false),
    'practitioner_portal'     => (bool) env('PRACTITIONER_PORTAL_ENABLED', false),

    // AI features
    'ai_assistant'            => (bool) env('AI_ASSISTANT_ENABLED', true),
    'ai_curriculum_gen'       => (bool) env('AI_CURRICULUM_GEN_ENABLED', false),
    'ai_video_gen'            => (bool) env('AI_VIDEO_GEN_ENABLED', false),
    'ai_media_gen'            => (bool) env('AI_MEDIA_GEN_ENABLED', false),

    // Commerce
    'subscriptions'           => (bool) env('SUBSCRIPTIONS_ENABLED', true),
    'referrals'               => (bool) env('REFERRALS_ENABLED', true),
    'scholarships'            => (bool) env('SCHOLARSHIPS_ENABLED', false),
    'teacher_marketplace'     => (bool) env('TEACHER_MARKETPLACE_ENABLED', false),
    'school_inquiries'        => (bool) env('SCHOOL_INQUIRIES_ENABLED', true),

    // Social / engagement
    'share_cards'             => (bool) env('SHARE_CARDS_ENABLED', true),
    'daily_digest_email'      => (bool) env('DAILY_DIGEST_ENABLED', false),
    'milestones'              => (bool) env('MILESTONES_ENABLED', true),
    'badges'                  => (bool) env('BADGES_ENABLED', true),

    // Live sessions
    'live_classes'            => (bool) env('LIVE_CLASSES_ENABLED', false),

    // Phase 1: Precision Curriculum Engine
    'phase1_metadata'         => (bool) env('PHASE1_METADATA_ENABLED', false),
    'phase1_feedback_loop'    => (bool) env('PHASE1_FEEDBACK_LOOP_ENABLED', false),
    'phase1_emotional_intel'  => (bool) env('PHASE1_EMOTIONAL_INTEL_ENABLED', false),

    // Viral Growth & Retention (Phase 1 flags — master plan breezy-honking-squid)
    'viral_referrals_v2'        => (bool) env('VIRAL_REFERRALS_V2', false),
    'public_share_pages'        => (bool) env('PUBLIC_SHARE_PAGES', false),
    'stripe_webhooks_v2'        => (bool) env('STRIPE_WEBHOOKS_V2', true),
    'notification_engine'       => (bool) env('NOTIFICATION_ENGINE', false),
    'push_notifications'        => (bool) env('PUSH_NOTIFICATIONS', false),
    'sidecar_circuit_breaker'   => (bool) env('SIDECAR_CIRCUIT_BREAKER', true),
    'sidecar_prewarm_pool'      => (bool) env('SIDECAR_PREWARM_POOL', false),
    'emotional_journey'         => (bool) env('EMOTIONAL_JOURNEY', false),
    'sms_notifications'         => (bool) env('SMS_NOTIFICATIONS', false),
    'native_push_apns'          => (bool) env('NATIVE_PUSH_APNS', false),
    'notification_ab_testing'   => (bool) env('NOTIFICATION_AB_TESTING', false),
    'paypal_v2'                 => (bool) env('PAYPAL_V2', false),

    // Phase 2: Thematic Cross-Curricular
    'phase2_journeys'         => (bool) env('PHASE2_JOURNEYS_ENABLED', false),
    'phase2_orchestration'    => (bool) env('PHASE2_ORCHESTRATION_ENABLED', false),

    // Phase 3: Privacy-First Ecosystem
    'phase3_health_enclave'   => (bool) env('PHASE3_HEALTH_ENCLAVE_ENABLED', false),
    'phase3_health_adapter'   => (bool) env('PHASE3_HEALTH_ADAPTER_ENABLED', false),
    'phase3_privacy_dashboard' => (bool) env('PHASE3_PRIVACY_DASHBOARD_ENABLED', false),

];
