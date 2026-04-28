<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$parent = \App\Models\User::where('email', 'parent@noblenest.test')->first();
$kids = \App\Models\ChildProfile::where('parent_id', $parent->id)->count();
echo "Parent (ID {$parent->id}) has {$kids} children\n";

if ($kids === 0) {
    \App\Models\ChildProfile::create([
        'parent_id'          => $parent->id,
        'name'               => 'Amina',
        'date_of_birth'      => now()->subMonths(36)->toDateString(),
        // preferred_language defaults to 'en' in the migration
    ]);
    echo "Created child: Amina (3 years old)\n";
}
