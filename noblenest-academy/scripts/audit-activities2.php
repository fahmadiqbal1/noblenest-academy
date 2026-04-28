<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Activity;

$subjects = ['social','art','motor','sensory','quran','arabic','literacy','stem','coding','numeracy','science','cultural','etiquette','character'];
foreach ($subjects as $s) {
    echo "\n=== SUBJECT: $s ===\n";
    $acts = Activity::where('subject', $s)->limit(3)->get(['id','title','activity_type','age_tier','description','instructions']);
    foreach ($acts as $a) {
        $instr = $a->getRawOriginal('instructions');
        echo "  [{$s}|{$a->activity_type}|{$a->age_tier}] {$a->id}: {$a->title}\n";
        echo "    desc: " . substr($a->description, 0, 100) . "\n";
        if ($instr) {
            echo "    instr: " . substr($instr, 0, 150) . "\n";
        } else {
            echo "    instr: NULL\n";
        }
    }
}

// Also show activity_type distribution
echo "\n=== Activity type counts ===\n";
Activity::selectRaw('activity_type, count(*) as cnt')
    ->groupBy('activity_type')
    ->orderByDesc('cnt')
    ->each(fn($r) => print("  {$r->activity_type}: {$r->cnt}\n"));
