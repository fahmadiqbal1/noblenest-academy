<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ContraindicationMatrix;
use App\Models\MaternalContent;
use App\Models\MaternalJournal;
use App\Models\MaternalProfile;
use App\Models\User;
use App\Services\MaternalContentFilterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaternalWellnessTest extends TestCase
{
    use RefreshDatabase;

    private User $parent;
    private User $admin;
    private User $otherParent;

    protected function setUp(): void
    {
        parent::setUp();

        // Enable the feature flag for tests
        config(['features.maternal_module' => true]);

        $this->parent      = User::factory()->create(['role' => 'Parent']);
        $this->admin       = User::factory()->create(['role' => 'Admin']);
        $this->otherParent = User::factory()->create(['role' => 'Parent']);
    }

    // ------------------------------------------------------------------
    // Feature Flag
    // ------------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function maternal_routes_return_404_when_feature_disabled(): void
    {
        config(['features.maternal_module' => false]);

        $this->actingAs($this->parent)
             ->get(route('maternal.onboarding'))
             ->assertNotFound();
    }

    // ------------------------------------------------------------------
    // Onboarding
    // ------------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function parent_can_view_onboarding_form(): void
    {
        $this->actingAs($this->parent)
             ->get(route('maternal.onboarding'))
             ->assertOk()
             ->assertSee('Maternal Wellness');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function parent_can_complete_onboarding(): void
    {
        $this->actingAs($this->parent)
             ->post(route('maternal.onboarding.store'), [
                 'due_date'            => now()->addMonths(5)->format('Y-m-d'),
                 'pregnancy_week'      => 18,
                 'health_conditions'   => ['none'],
                 'dietary_restrictions' => ['vegetarian'],
                 'consent_accepted'    => true,
             ])
             ->assertRedirect(route('maternal.dashboard'));

        $this->assertDatabaseHas('maternal_profiles', [
            'user_id' => $this->parent->id,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function onboarding_requires_consent(): void
    {
        $this->actingAs($this->parent)
             ->post(route('maternal.onboarding.store'), [
                 'due_date'        => now()->addMonths(5)->format('Y-m-d'),
                 'pregnancy_week'  => 18,
                 'health_conditions' => [],
             ])
             ->assertSessionHasErrors('consent_accepted');
    }

    // ------------------------------------------------------------------
    // Consent Middleware
    // ------------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function dashboard_redirects_to_onboarding_without_profile(): void
    {
        $this->actingAs($this->parent)
             ->get(route('maternal.dashboard'))
             ->assertRedirect(route('maternal.onboarding'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function dashboard_accessible_with_profile(): void
    {
        $this->createProfileForParent($this->parent);

        $this->actingAs($this->parent)
             ->get(route('maternal.dashboard'))
             ->assertOk();
    }

    // ------------------------------------------------------------------
    // Content Viewing
    // ------------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function parent_can_view_published_approved_content(): void
    {
        $this->createProfileForParent($this->parent);

        $content = MaternalContent::create([
            'title'              => 'Test Herb Guide',
            'slug'               => 'test-herb-guide',
            'description'        => 'A test herb guide.',
            'benefit_explanation' => 'Helps with testing.',
            'skills_improved'    => ['wellness'],
            'content_type'       => 'herb_guide',
            'stage'              => 'trimester_1',
            'category'           => 'herbs',
            'is_published'       => true,
            'moderation_status'  => 'approved',
            'language'           => 'en',
        ]);

        $this->actingAs($this->parent)
             ->get(route('maternal.content.show', $content))
             ->assertOk()
             ->assertSee('Test Herb Guide');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function parent_cannot_view_pending_content(): void
    {
        $this->createProfileForParent($this->parent);

        $content = MaternalContent::create([
            'title'              => 'Pending Content',
            'slug'               => 'pending-content',
            'description'        => 'Not yet approved.',
            'benefit_explanation' => 'Pending.',
            'skills_improved'    => ['wellness'],
            'content_type'       => 'article',
            'stage'              => 'trimester_1',
            'category'           => 'technique',
            'is_published'       => true,
            'moderation_status'  => 'pending',
            'language'           => 'en',
        ]);

        $this->actingAs($this->parent)
             ->get(route('maternal.content.show', $content))
             ->assertForbidden();
    }

    // ------------------------------------------------------------------
    // Contraindication Filtering
    // ------------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function content_filter_service_excludes_contraindicated_items(): void
    {
        $profile = $this->createProfileForParent($this->parent, ['gestational_diabetes']);

        $safeContent = MaternalContent::create([
            'title' => 'Safe Content', 'slug' => 'safe-content',
            'description' => 'Safe.', 'benefit_explanation' => 'Safe.',
            'skills_improved' => ['wellness'],
            'content_type' => 'herb_guide', 'stage' => 'trimester_2',
            'category' => 'herbs', 'is_published' => true,
            'moderation_status' => 'approved', 'language' => 'en',
        ]);

        $unsafeContent = MaternalContent::create([
            'title' => 'Fenugreek', 'slug' => 'fenugreek-test',
            'description' => 'Fenugreek seeds.', 'benefit_explanation' => 'Milk boost.',
            'skills_improved' => ['nutrition'],
            'content_type' => 'herb_guide', 'stage' => 'trimester_2',
            'category' => 'herbs', 'is_published' => true,
            'moderation_status' => 'approved', 'language' => 'en',
        ]);

        ContraindicationMatrix::create([
            'maternal_content_id' => $unsafeContent->id,
            'condition'           => 'gestational_diabetes',
            'reason'              => 'May interact with blood sugar meds.',
        ]);

        $filter = app(MaternalContentFilterService::class);
        $results = $filter->safeContentQuery($profile)->get();

        $this->assertTrue($results->contains('id', $safeContent->id));
        $this->assertFalse($results->contains('id', $unsafeContent->id));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function is_safe_returns_false_for_contraindicated_content(): void
    {
        $profile = $this->createProfileForParent($this->parent, ['hypertension']);

        $content = MaternalContent::create([
            'title' => 'Warm Bath', 'slug' => 'warm-bath-test',
            'description' => 'Bath therapy.', 'benefit_explanation' => 'Relaxation.',
            'skills_improved' => ['relaxation'],
            'content_type' => 'technique', 'stage' => 'trimester_2',
            'category' => 'technique', 'is_published' => true,
            'moderation_status' => 'approved', 'language' => 'en',
        ]);

        ContraindicationMatrix::create([
            'maternal_content_id' => $content->id,
            'condition'           => 'hypertension',
            'reason'              => 'Warm water lowers blood pressure further.',
        ]);

        $filter = app(MaternalContentFilterService::class);
        $this->assertFalse($filter->isSafe($content, $profile));
    }

    // ------------------------------------------------------------------
    // Journal
    // ------------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function parent_can_create_journal_entry(): void
    {
        $profile = $this->createProfileForParent($this->parent);

        $this->actingAs($this->parent)
             ->post(route('maternal.journal.store'), [
                 'entry_date'     => now()->toDateString(),
                 'mood'           => 'great',
                 'energy_level'   => 4,
                 'baby_kicks'     => 12,
                 'symptoms'       => ['fatigue'],
                 'notes'          => 'Feeling great today!',
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('maternal_journals', [
            'maternal_profile_id' => $profile->id,
            'mood'                => 'great',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function journal_with_alert_symptoms_redirects_to_emergency(): void
    {
        $profile = $this->createProfileForParent($this->parent);

        $response = $this->actingAs($this->parent)
             ->post(route('maternal.journal.store'), [
                 'entry_date' => now()->toDateString(),
                 'mood'       => 'low',
                 'energy_level' => 2,
                 'symptoms'   => ['bleeding', 'severe headache'],
                 'notes'      => 'Very worried.',
             ]);

        $response->assertRedirect(route('maternal.emergency-signs'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function parent_cannot_view_other_parents_journal(): void
    {
        $profile = $this->createProfileForParent($this->parent);
        $otherProfile = $this->createProfileForParent($this->otherParent);

        $journal = MaternalJournal::create([
            'maternal_profile_id' => $otherProfile->id,
            'mood'                => 'calm',
            'entry_date'          => now()->toDateString(),
            'week_number'         => 20,
        ]);

        $this->actingAs($this->parent)
             ->get(route('maternal.journal.show', $journal))
             ->assertForbidden();
    }

    // ------------------------------------------------------------------
    // Profile Ownership
    // ------------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function parent_can_pause_own_journey(): void
    {
        $profile = $this->createProfileForParent($this->parent);

        $this->actingAs($this->parent)
             ->post(route('maternal.profile.pause'))
             ->assertRedirect();

        $this->assertEquals('paused', $profile->fresh()->status);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function parent_can_resume_paused_journey(): void
    {
        $profile = $this->createProfileForParent($this->parent);
        $profile->update(['status' => 'paused']);

        $this->actingAs($this->parent)
             ->post(route('maternal.profile.resume'))
             ->assertRedirect();

        $this->assertEquals('active', $profile->fresh()->status);
    }

    // ------------------------------------------------------------------
    // Admin Content Management
    // ------------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_view_content_management(): void
    {
        $this->actingAs($this->admin)
             ->get(route('admin.maternal.content.index'))
             ->assertOk();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_approve_content(): void
    {
        $content = MaternalContent::create([
            'title' => 'Needs Approval', 'slug' => 'needs-approval',
            'description' => 'Pending.', 'benefit_explanation' => 'Testing.',
            'skills_improved' => ['wellness'],
            'content_type' => 'article', 'stage' => 'trimester_1',
            'category' => 'technique', 'is_published' => true,
            'moderation_status' => 'pending', 'language' => 'en',
        ]);

        $this->actingAs($this->admin)
             ->post(route('admin.maternal.content.approve', $content), [
                 'medical_reviewer_name' => 'Dr. Test Reviewer',
             ])
             ->assertRedirect();

        $this->assertEquals('approved', $content->fresh()->moderation_status);
        $this->assertEquals('Dr. Test Reviewer', $content->fresh()->medical_reviewer_name);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_reject_content(): void
    {
        $content = MaternalContent::create([
            'title' => 'Reject This', 'slug' => 'reject-this',
            'description' => 'Bad.', 'benefit_explanation' => 'None.',
            'skills_improved' => ['wellness'],
            'content_type' => 'article', 'stage' => 'trimester_1',
            'category' => 'technique', 'is_published' => true,
            'moderation_status' => 'pending', 'language' => 'en',
        ]);

        $this->actingAs($this->admin)
             ->post(route('admin.maternal.content.reject', $content))
             ->assertRedirect();

        $this->assertEquals('rejected', $content->fresh()->moderation_status);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function non_admin_cannot_access_admin_content(): void
    {
        $this->actingAs($this->parent)
             ->get(route('admin.maternal.content.index'))
             ->assertForbidden();
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    private function createProfileForParent(User $parent, array $conditions = []): MaternalProfile
    {
        $profile = new MaternalProfile();
        $profile->user_id            = $parent->id;
        $profile->due_date           = now()->addMonths(4)->toDateString();
        $profile->consent_accepted_at = now();
        $profile->status             = 'active';
        $profile->health_conditions  = $conditions ?: null;
        $profile->dietary_restrictions = [];
        $profile->save();

        return $profile;
    }
}
