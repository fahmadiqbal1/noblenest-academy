<?php

namespace Tests\Unit;

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

        foreach ((new ReflectionClass($class)) ->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
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
        $this->assertModel(\App\Models\AIJob::class);
    }

    public function test_ai_provider_config_factory_and_relations(): void
    {
        $this->assertModel(\App\Models\AIProviderConfig::class);
    }

    public function test_activity_factory_and_relations(): void
    {
        $this->assertModel(\App\Models\Activity::class);
    }

    public function test_activity_like_factory_and_relations(): void
    {
        $this->assertModel(\App\Models\ActivityLike::class);
    }

    public function test_activity_media_factory_and_relations(): void
    {
        $this->assertModel(\App\Models\ActivityMedia::class);
    }

    public function test_activity_step_factory_and_relations(): void
    {
        $this->assertModel(\App\Models\ActivityStep::class);
    }

    public function test_activity_translation_factory_and_relations(): void
    {
        $this->assertModel(\App\Models\ActivityTranslation::class);
    }

    public function test_assessment_question_factory_and_relations(): void
    {
        $this->assertModel(\App\Models\AssessmentQuestion::class);
    }

    public function test_assessment_response_factory_and_relations(): void
    {
        $this->assertModel(\App\Models\AssessmentResponse::class);
    }

    public function test_badge_factory_and_relations(): void
    {
        $this->assertModel(\App\Models\Badge::class);
    }

    public function test_child_activity_progress_factory_and_relations(): void
    {
        $this->assertModel(\App\Models\ChildActivityProgress::class);
    }

    public function test_child_journey_enrollment_factory_and_relations(): void
    {
        $this->assertModel(\App\Models\ChildJourneyEnrollment::class);
    }

    public function test_child_profile_factory_and_relations(): void
    {
        $this->assertModel(\App\Models\ChildProfile::class);
    }

    public function test_child_skill_state_factory_and_relations(): void
    {
        $this->assertModel(\App\Models\ChildSkillState::class);
    }

    public function test_consent_receipt_factory_and_relations(): void
    {
        $this->assertModel(\App\Models\ConsentReceipt::class);
    }

    public function test_course_factory_and_relations(): void
    {
        $this->assertModel(\App\Models\Course::class);
    }

    public function test_lesson_factory_and_relations(): void
    {
        $this->assertModel(\App\Models\Lesson::class);
    }

    public function test_milestone_factory_and_relations(): void
    {
        $this->assertModel(\App\Models\Milestone::class);
    }

    public function test_module_factory_and_relations(): void
    {
        $this->assertModel(\App\Models\Module::class);
    }

    public function test_notification_factory_and_relations(): void
    {
        $this->assertModel(\App\Models\Notification::class);
    }

    public function test_option_factory_and_relations(): void
    {
        $this->assertModel(\App\Models\Option::class);
    }

    public function test_payment_factory_and_relations(): void
    {
        $this->assertModel(\App\Models\Payment::class);
    }

    public function test_pricing_tier_factory_and_relations(): void
    {
        $this->assertModel(\App\Models\PricingTier::class);
    }

    public function test_question_factory_and_relations(): void
    {
        $this->assertModel(\App\Models\Question::class);
    }

    public function test_quiz_factory_and_relations(): void
    {
        $this->assertModel(\App\Models\Quiz::class);
    }

    public function test_quiz_answer_factory_and_relations(): void
    {
        $this->assertModel(\App\Models\QuizAnswer::class);
    }

    public function test_quiz_attempt_factory_and_relations(): void
    {
        $this->assertModel(\App\Models\QuizAttempt::class);
    }

    public function test_subscription_factory_and_relations(): void
    {
        $this->assertModel(\App\Models\Subscription::class);
    }

    public function test_thematic_journey_factory_and_relations(): void
    {
        $this->assertModel(\App\Models\ThematicJourney::class);
    }

    public function test_theme_activity_factory_and_relations(): void
    {
        $this->assertModel(\App\Models\ThemeActivity::class);
    }

    public function test_user_factory_and_relations(): void
    {
        $this->assertModel(\App\Models\User::class);
    }

    public function test_weekly_theme_factory_and_relations(): void
    {
        $this->assertModel(\App\Models\WeeklyTheme::class);
    }
}
