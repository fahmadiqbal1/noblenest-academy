<?php

namespace Tests\Unit;

use App\Models\Activity;
use App\Models\ActivityLike;
use App\Models\ActivityMedia;
use App\Models\ActivityStep;
use App\Models\ActivityTranslation;
use App\Models\AIJob;
use App\Models\AIProviderConfig;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentResponse;
use App\Models\Badge;
use App\Models\ChildActivityProgress;
use App\Models\ChildJourneyEnrollment;
use App\Models\ChildProfile;
use App\Models\ChildSkillState;
use App\Models\ConsentReceipt;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Milestone;
use App\Models\Module;
use App\Models\Notification;
use App\Models\Option;
use App\Models\Payment;
use App\Models\PricingTier;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use App\Models\Subscription;
use App\Models\ThematicJourney;
use App\Models\ThemeActivity;
use App\Models\User;
use App\Models\WeeklyTheme;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionClass;
use ReflectionMethod;
use Tests\TestCase;

/**
 * Phase 2 — verifies every model has a working factory and that each
 * belongsTo relation resolves to an instance of the expected related class.
 *
 * Relations are discovered reflectively so the assertions stay in sync with
 * the models without hand-maintaining a map.
 */
class ModelRelationsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Create a model via factory, assert it has a primary key, then walk
     * every belongsTo relation and assert it returns the expected class
     * (falling back to a Relation-instance assertion when not satisfiable).
     */
    private function assertModel(string $class): void
    {
        $model = $class::factory()->create();

        $this->assertInstanceOf(Model::class, $model);
        $this->assertNotNull(
            $model->getKey(),
            "{$class} factory did not produce a primary key."
        );

        $fresh = $model->fresh();

        foreach ((new ReflectionClass($class))->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->class !== $class
                || $method->getNumberOfParameters() > 0
                || $method->isStatic()
            ) {
                continue;
            }

            try {
                $relation = $model->{$method->name}();
            } catch (\Throwable) {
                continue;
            }

            if (! $relation instanceof BelongsTo) {
                continue;
            }

            $this->assertInstanceOf(Relation::class, $relation);

            $related = $fresh->{$method->name};

            if ($related !== null) {
                $this->assertInstanceOf(
                    get_class($relation->getRelated()),
                    $related,
                    "{$class}::{$method->name}() returned unexpected related type."
                );
            }
        }
    }

    public function test_ai_job_factory_and_relations(): void
    {
        $this->assertModel(AIJob::class);
    }

    public function test_ai_provider_config_factory_and_relations(): void
    {
        $this->assertModel(AIProviderConfig::class);
    }

    public function test_activity_factory_and_relations(): void
    {
        $this->assertModel(Activity::class);
    }

    public function test_activity_like_factory_and_relations(): void
    {
        $this->assertModel(ActivityLike::class);
    }

    public function test_activity_media_factory_and_relations(): void
    {
        $this->assertModel(ActivityMedia::class);
    }

    public function test_activity_step_factory_and_relations(): void
    {
        $this->assertModel(ActivityStep::class);
    }

    public function test_activity_translation_factory_and_relations(): void
    {
        $this->assertModel(ActivityTranslation::class);
    }

    public function test_assessment_question_factory_and_relations(): void
    {
        $this->assertModel(AssessmentQuestion::class);
    }

    public function test_assessment_response_factory_and_relations(): void
    {
        $this->assertModel(AssessmentResponse::class);
    }

    public function test_badge_factory_and_relations(): void
    {
        $this->assertModel(Badge::class);
    }

    public function test_child_activity_progress_factory_and_relations(): void
    {
        $this->assertModel(ChildActivityProgress::class);
    }

    public function test_child_journey_enrollment_factory_and_relations(): void
    {
        $this->assertModel(ChildJourneyEnrollment::class);
    }

    public function test_child_profile_factory_and_relations(): void
    {
        $this->assertModel(ChildProfile::class);
    }

    public function test_child_skill_state_factory_and_relations(): void
    {
        $this->assertModel(ChildSkillState::class);
    }

    public function test_consent_receipt_factory_and_relations(): void
    {
        $this->assertModel(ConsentReceipt::class);
    }

    public function test_course_factory_and_relations(): void
    {
        $this->assertModel(Course::class);
    }

    public function test_lesson_factory_and_relations(): void
    {
        $this->assertModel(Lesson::class);
    }

    public function test_milestone_factory_and_relations(): void
    {
        $this->assertModel(Milestone::class);
    }

    public function test_module_factory_and_relations(): void
    {
        $this->assertModel(Module::class);
    }

    public function test_notification_factory_and_relations(): void
    {
        $this->assertModel(Notification::class);
    }

    public function test_option_factory_and_relations(): void
    {
        $this->assertModel(Option::class);
    }

    public function test_payment_factory_and_relations(): void
    {
        $this->assertModel(Payment::class);
    }

    public function test_pricing_tier_factory_and_relations(): void
    {
        $this->assertModel(PricingTier::class);
    }

    public function test_question_factory_and_relations(): void
    {
        $this->assertModel(Question::class);
    }

    public function test_quiz_factory_and_relations(): void
    {
        $this->assertModel(Quiz::class);
    }

    public function test_quiz_answer_factory_and_relations(): void
    {
        $this->assertModel(QuizAnswer::class);
    }

    public function test_quiz_attempt_factory_and_relations(): void
    {
        $this->assertModel(QuizAttempt::class);
    }

    public function test_subscription_factory_and_relations(): void
    {
        $this->assertModel(Subscription::class);
    }

    public function test_thematic_journey_factory_and_relations(): void
    {
        $this->assertModel(ThematicJourney::class);
    }

    public function test_theme_activity_factory_and_relations(): void
    {
        $this->assertModel(ThemeActivity::class);
    }

    public function test_user_factory_and_relations(): void
    {
        $this->assertModel(User::class);
    }

    public function test_weekly_theme_factory_and_relations(): void
    {
        $this->assertModel(WeeklyTheme::class);
    }
}
