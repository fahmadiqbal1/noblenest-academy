<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminActivityCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'Admin']);
    }

    // ------------------------------------------------------------------
    // Index & search
    // ------------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_list_activities(): void
    {
        Activity::factory()->count(3)->create();

        $this->actingAs($this->admin)
             ->get('/admin/activities')
             ->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_search_activities(): void
    {
        Activity::factory()->create(['title' => 'Counting Fun', 'subject' => 'math']);
        Activity::factory()->create(['title' => 'Letter Tracing', 'subject' => 'literacy']);

        $response = $this->actingAs($this->admin)->get('/admin/activities?q=Counting');
        $response->assertStatus(200);
        $response->assertSee('Counting Fun');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_filter_activities_by_subject(): void
    {
        Activity::factory()->create(['subject' => 'math']);
        Activity::factory()->create(['subject' => 'literacy']);

        $this->actingAs($this->admin)
             ->get('/admin/activities?subject=math')
             ->assertStatus(200);
    }

    // ------------------------------------------------------------------
    // Create
    // ------------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_view_create_form(): void
    {
        $this->actingAs($this->admin)
             ->get('/admin/activities/create')
             ->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_create_activity(): void
    {
        $this->actingAs($this->admin)
             ->post('/admin/activities', [
                 'title'         => 'New Activity',
                 'description'   => 'A fun new activity for kids.',
                 'activity_type' => 'drawing',
                 'age_min'       => 3,
                 'age_max'       => 5,
                 'subject'       => 'creative',
                 'difficulty'    => 'easy',
                 'language'      => 'en',
             ])
             ->assertRedirect(route('admin.activities.index'));

        $this->assertDatabaseHas('activities', ['title' => 'New Activity', 'activity_type' => 'drawing']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function create_activity_requires_title_and_type(): void
    {
        $this->actingAs($this->admin)
             ->post('/admin/activities', ['description' => 'Missing required fields'])
             ->assertSessionHasErrors(['title', 'activity_type']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function create_activity_with_media_uses_uuid_filename(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)
             ->post('/admin/activities', [
                 'title'         => 'Media Activity',
                 'description'   => 'Activity with media.',
                 'activity_type' => 'video',
                 'media_file'    => UploadedFile::fake()->create('lesson.mp4', 1024, 'video/mp4'),
             ]);

        $activity = Activity::where('title', 'Media Activity')->first();
        $this->assertNotNull($activity);
        $this->assertNotNull($activity->media_url);
        // UUID filename should NOT contain original user filename
        $this->assertStringNotContainsString('lesson', $activity->media_url);
        // Should still have .mp4 extension
        $this->assertStringEndsWith('.mp4', $activity->media_url);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function create_activity_rejects_disallowed_file_types(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)
             ->post('/admin/activities', [
                 'title'         => 'Bad File',
                 'description'   => 'Activity with bad file.',
                 'activity_type' => 'video',
                 'media_file'    => UploadedFile::fake()->create('malware.exe', 100, 'application/x-msdownload'),
             ])
             ->assertSessionHasErrors('media_file');
    }

    // ------------------------------------------------------------------
    // Update
    // ------------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_update_activity(): void
    {
        $activity = Activity::factory()->create(['title' => 'Old Title']);

        $this->actingAs($this->admin)
             ->put("/admin/activities/{$activity->id}", [
                 'title'         => 'Updated Title',
                 'description'   => 'Updated description.',
                 'activity_type' => 'quiz',
             ])
             ->assertRedirect(route('admin.activities.index'));

        $this->assertEquals('Updated Title', $activity->fresh()->title);
    }

    // ------------------------------------------------------------------
    // Delete
    // ------------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_delete_activity(): void
    {
        $activity = Activity::factory()->create();

        $this->actingAs($this->admin)
             ->delete("/admin/activities/{$activity->id}")
             ->assertRedirect(route('admin.activities.index'));

        $this->assertDatabaseMissing('activities', ['id' => $activity->id]);
    }

    // ------------------------------------------------------------------
    // Bulk upload / CSV
    // ------------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_bulk_upload_csv(): void
    {
        $csv = "title,description,activity_type,age_min,age_max,subject\n";
        $csv .= "CSV Activity 1,First activity,drawing,3,5,creative\n";
        $csv .= "CSV Activity 2,Second activity,quiz,4,6,math\n";

        $file = UploadedFile::fake()->createWithContent('activities.csv', $csv);

        $this->actingAs($this->admin)
             ->post('/admin/activities/bulk-upload', ['file' => $file])
             ->assertRedirect();

        $this->assertDatabaseHas('activities', ['title' => 'CSV Activity 1']);
        $this->assertDatabaseHas('activities', ['title' => 'CSV Activity 2']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function csv_bulk_upload_sanitizes_html(): void
    {
        $csv = "title,description,activity_type\n";
        $csv .= "<script>alert('xss')</script>,Malicious,drawing\n";

        $file = UploadedFile::fake()->createWithContent('malicious.csv', $csv);

        $this->actingAs($this->admin)
             ->post('/admin/activities/bulk-upload', ['file' => $file]);

        // Title should be sanitized, not contain raw script tags
        $activity = Activity::first();
        if ($activity) {
            $this->assertStringNotContainsString('<script>', $activity->title);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function csv_bulk_upload_skips_rows_without_title(): void
    {
        $csv = "title,description,activity_type\n";
        $csv .= ",No title here,drawing\n";
        $csv .= "Valid Activity,Has title,quiz\n";

        $file = UploadedFile::fake()->createWithContent('partial.csv', $csv);

        $this->actingAs($this->admin)
             ->post('/admin/activities/bulk-upload', ['file' => $file]);

        $this->assertDatabaseHas('activities', ['title' => 'Valid Activity']);
        $this->assertEquals(1, Activity::count());
    }

    // ------------------------------------------------------------------
    // Role protection
    // ------------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function non_admin_cannot_access_activity_crud(): void
    {
        $parent = User::factory()->create(['role' => 'Parent']);

        $this->actingAs($parent)->get('/admin/activities')
             ->assertStatus(403);

        $this->actingAs($parent)->post('/admin/activities', [
            'title' => 'Hack', 'description' => 'x', 'activity_type' => 'quiz',
        ])->assertStatus(403);
    }
}
