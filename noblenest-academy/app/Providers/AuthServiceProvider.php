<?php

namespace App\Providers;

use App\Models\ActivityLike;
use App\Models\AssessmentResponse;
use App\Models\ChildActivityProgress;
use App\Models\ChildJourneyEnrollment;
use App\Models\ChildProfile;
use App\Models\ChildSkillState;
use App\Models\ConsentReceipt;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use App\Policies\ActivityLikePolicy;
use App\Policies\AssessmentResponsePolicy;
use App\Policies\ChildActivityProgressPolicy;
use App\Policies\ChildJourneyEnrollmentPolicy;
use App\Policies\ChildProfilePolicy;
use App\Policies\ChildSkillStatePolicy;
use App\Policies\ConsentReceiptPolicy;
use App\Policies\QuizAnswerPolicy;
use App\Policies\QuizAttemptPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Every model that touches child PII or learning state is deny-by-default.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        ChildProfile::class => ChildProfilePolicy::class,
        ChildActivityProgress::class => ChildActivityProgressPolicy::class,
        ChildJourneyEnrollment::class => ChildJourneyEnrollmentPolicy::class,
        ChildSkillState::class => ChildSkillStatePolicy::class,
        ConsentReceipt::class => ConsentReceiptPolicy::class,
        AssessmentResponse::class => AssessmentResponsePolicy::class,
        QuizAttempt::class => QuizAttemptPolicy::class,
        QuizAnswer::class => QuizAnswerPolicy::class,
        ActivityLike::class => ActivityLikePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
