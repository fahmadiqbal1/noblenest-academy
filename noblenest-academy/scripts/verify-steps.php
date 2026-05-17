<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Activity;
use Illuminate\Contracts\Console\Kernel;

// Verify Bath Play Together
$a = Activity::with('steps')->find(96);
echo "=== Bath Play Together ===\n";
echo "Subject: {$a->subject}, Type: {$a->activity_type}\n";
foreach ($a->steps as $s) {
    echo "  Step {$s->step_number}: {$s->title}\n";
    echo "    {$s->instruction}\n";
    echo "    Benefit: {$s->benefit_note}\n\n";
}

// Sample a quran activity
$q = Activity::with('steps')->where('subject', 'quran')->first();
echo "\n=== QURAN: {$q->title} ===\n";
foreach ($q->steps as $s) {
    echo "  Step {$s->step_number}: {$s->title} — ".substr($s->instruction, 0, 80)."...\n";
}

// Sample a motor activity
$m = Activity::with('steps')->where('subject', 'motor')->first();
echo "\n=== MOTOR: {$m->title} ===\n";
foreach ($m->steps as $s) {
    echo "  Step {$s->step_number}: {$s->title} — ".substr($s->instruction, 0, 80)."...\n";
}

// Sample a coding activity
$c = Activity::with('steps')->where('subject', 'coding')->first();
echo "\n=== CODING: {$c->title} ===\n";
foreach ($c->steps as $s) {
    echo "  Step {$s->step_number}: {$s->title} — ".substr($s->instruction, 0, 80)."...\n";
}

// Sample activity WITH parsed instructions
$pi = Activity::with('steps')->whereNotNull('instructions')->first();
echo "\n=== PARSED INSTRUCTIONS: {$pi->title} (subject: {$pi->subject}) ===\n";
echo 'Raw: '.substr($pi->getRawOriginal('instructions'), 0, 200)."\n\n";
foreach ($pi->steps as $s) {
    echo "  Step {$s->step_number}: {$s->title}\n    {$s->instruction}\n";
}
