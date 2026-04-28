<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Activity;

$activity = Activity::with('steps')->find(96);
echo "Activity: {$activity->title}\n";
echo "Steps count: {$activity->steps->count()}\n";
echo "Subject: {$activity->subject}\n";
echo "Emoji: {$activity->emoji}\n";
echo "Materials: " . gettype($activity->materials_needed) . " = " . json_encode($activity->materials_needed) . "\n";
echo "Learning objectives: " . gettype($activity->learning_objectives) . "\n";

// Try to render the component PHP logic
$subject = $activity->subject ?? 'default';
$subjectPalettes = [
    'social' => ['from' => '#0E7490', 'to' => '#67E8F9'],
];
$pal = $subjectPalettes[$subject] ?? ['from' => '#4C1D95', 'to' => '#A78BFA'];
echo "Palette: from={$pal['from']} to={$pal['to']}\n";

// Test the mapWithKeys logic
$allSteps = $activity->steps->sortBy('step_number')->values();
$stepVisuals = [
    1 => ['emoji' => '🎒', 'label' => 'Get Ready',     'anim' => 'nn-anim-bounce'],
    2 => ['emoji' => '👀', 'label' => 'Watch & Learn',  'anim' => 'nn-anim-pulse'],
    3 => ['emoji' => '🤲', 'label' => 'Try It!',        'anim' => 'nn-anim-wiggle'],
    4 => ['emoji' => '💬', 'label' => 'Talk About It',  'anim' => 'nn-anim-float'],
    5 => ['emoji' => '🎉', 'label' => 'Celebrate!',     'anim' => 'nn-anim-spin'],
];
$stepVisualsMap = [];
foreach ($allSteps as $idx => $s) {
    $sn  = (int) $s->step_number;
    $key = (($sn - 1) % 8) + 1;
    $viz = $stepVisuals[$sn] ?? $stepVisuals[$key] ?? ['emoji' => '⭐', 'label' => 'Step ' . $sn, 'anim' => 'nn-anim-pulse'];
    $stepVisualsMap[$idx] = $viz;
}
echo "Step visuals map: " . json_encode($stepVisualsMap) . "\n";

// Test view compilation
try {
    $user = App\Models\User::where('email', 'parent@noblenest.test')->first();
    auth()->login($user);
    $html = view('activities.show', ['activity' => $activity])->render();
    echo "\nView rendered successfully! Length: " . strlen($html) . " chars\n";
    echo "nn-step-player found: " . (str_contains($html, 'nn-step-player') ? 'YES' : 'NO') . "\n";
    echo "Come Together found: " . (str_contains($html, 'Come Together') ? 'YES' : 'NO') . "\n";
    echo "Guided Walkthrough found: " . (str_contains($html, 'Guided Walkthrough') ? 'YES' : 'NO') . "\n";
} catch (\Exception $e) {
    echo "\nVIEW ERROR: " . $e->getMessage() . "\n";
    echo "In: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
