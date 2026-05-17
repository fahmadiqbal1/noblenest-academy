<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Activity;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

// Delete steps only for activities that have instructions (the 599 that were parsed)
$ids = Activity::whereNotNull('instructions')->pluck('id');
$deleted = DB::table('activity_steps')->whereIn('activity_id', $ids)->delete();
echo "Deleted $deleted steps from ".count($ids)." activities with instructions (will re-seed with fixed parser).\n";

$remaining = DB::table('activity_steps')->count();
echo "Remaining steps: $remaining\n";
