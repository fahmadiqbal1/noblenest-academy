<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Activity;
use Illuminate\Support\Facades\DB;

$total         = Activity::count();
$noInstruct    = Activity::whereNull('instructions')->count();
$withInstruct  = Activity::whereNotNull('instructions')->count();
$noSteps       = Activity::doesntHave('steps')->count();
$hasSteps      = Activity::has('steps')->count();
$totalSteps    = DB::table('activity_steps')->count();

echo "=== Activity Content Audit ===\n";
echo "Total activities:         $total\n";
echo "Has instructions (JSON):  $withInstruct\n";
echo "Missing instructions:     $noInstruct\n";
echo "Has activity_steps rows:  $hasSteps\n";
echo "Missing steps entirely:   $noSteps ($noInstruct no instructions AND no steps)\n";
echo "Total activity_steps rows: $totalSteps\n";

// Sample 5 with instructions
echo "\n--- Sample 3 activities WITH instructions ---\n";
Activity::whereNotNull('instructions')->limit(3)->get(['id','title','instructions'])->each(function($a) {
    $val = $a->getRawOriginal('instructions');
    echo "  [{$a->id}] {$a->title}: ".substr($val, 0, 100)."\n";
});

// Sample 5 subjects breakdown
echo "\n--- Subject breakdown (no instructions) ---\n";
Activity::whereNull('instructions')
    ->selectRaw('subject, count(*) as cnt')
    ->groupBy('subject')
    ->orderByDesc('cnt')
    ->get()
    ->each(fn($r) => print("  {$r->subject}: {$r->cnt}\n"));
