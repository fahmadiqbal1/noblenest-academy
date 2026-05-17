<?php

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;

// One-time script: give parent test user an active subscription
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$parent = User::where('email', 'parent@noblenest.test')->first();
if (! $parent) {
    echo "Parent user not found\n";
    exit(1);
}

$sub = Subscription::firstOrCreate(
    ['user_id' => $parent->id],
    [
        'plan' => 'family',
        'provider' => 'seed',
        'provider_id' => 'seed-'.uniqid(),
        'amount' => 2500,
        'currency' => 'usd',
        'starts_at' => now(),
        'ends_at' => now()->addYear(),
        'active' => true,
    ]
);

echo "Parent subscription: plan={$sub->plan}, active={$sub->active}, ends={$sub->ends_at}\n";
