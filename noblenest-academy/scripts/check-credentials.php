<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$users = ['admin@noblenest.test', 'parent@noblenest.test', 'teacher@noblenest.test', 'student@noblenest.test'];
foreach ($users as $email) {
    $u = \App\Models\User::where('email', $email)->first();
    if (!$u) { echo "NOT FOUND: $email\n"; continue; }
    $ok = \Illuminate\Support\Facades\Hash::check('Password1!', $u->password);
    echo ($ok ? 'PASS' : 'FAIL') . " | {$email} | role={$u->role} | verified=" . ($u->email_verified_at ? 'yes' : 'no') . "\n";
}
