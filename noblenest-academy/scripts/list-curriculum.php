<?php
// Quick script to list all activities and maternal content
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Activity;
use App\Models\MaternalContent;
use App\Models\Course;
use App\Models\Module;

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║          NOBLE NEST ACADEMY — FULL CURRICULUM AUDIT         ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

// Activities
$activities = Activity::orderBy('subject')->orderBy('age_tier')->get();
echo "📚 ACTIVITIES ({$activities->count()} total)\n";
echo str_repeat('─', 60) . "\n";

foreach ($activities->groupBy('subject') as $subject => $items) {
    echo "\n  🎯 " . strtoupper($subject) . " ({$items->count()} activities)\n";
    foreach ($items as $a) {
        $free = $a->is_free ? '🆓' : '💰';
        $pub  = $a->published ? '✅' : '📝';
        echo "     {$pub} {$free} [{$a->age_tier}] {$a->title} ({$a->language})\n";
    }
}

echo "\n\n" . str_repeat('═', 60) . "\n";
echo "  Subjects: " . $activities->pluck('subject')->unique()->sort()->implode(', ') . "\n";
echo "  Age Tiers: " . $activities->pluck('age_tier')->unique()->sort()->implode(', ') . "\n";
echo "  Languages: " . $activities->pluck('language')->unique()->sort()->implode(', ') . "\n";

// Maternal Content
$maternal = MaternalContent::orderBy('stage')->orderBy('content_type')->get();
echo "\n\n🤰 MATERNAL WELLNESS CONTENT ({$maternal->count()} total)\n";
echo str_repeat('─', 60) . "\n";

foreach ($maternal->groupBy('stage') as $stage => $items) {
    echo "\n  🌸 " . strtoupper(str_replace('_', ' ', $stage)) . " ({$items->count()} items)\n";
    foreach ($items as $c) {
        $pub = ($c->is_published && $c->moderation_status === 'approved') ? '✅' : '📝';
        echo "     {$pub} [{$c->content_type}] {$c->title} ({$c->language})\n";
    }
}

// Courses
$courses = Course::with('modules')->get();
echo "\n\n🎓 COURSES ({$courses->count()} total)\n";
echo str_repeat('─', 60) . "\n";
foreach ($courses as $course) {
    $pub = $course->is_published ? '✅' : '📝';
    echo "  {$pub} {$course->title}\n";
    foreach ($course->modules as $mod) {
        echo "     └── {$mod->title} (order: {$mod->order})\n";
    }
}

echo "\n" . str_repeat('═', 60) . "\n";
echo "SUMMARY:\n";
echo "  Activities:       {$activities->count()}\n";
echo "  Maternal Content: {$maternal->count()}\n";
echo "  Courses:          {$courses->count()}\n";
echo "  Total Items:      " . ($activities->count() + $maternal->count() + $courses->count()) . "\n";
