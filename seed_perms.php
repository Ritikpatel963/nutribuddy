<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$managePerms = \Spatie\Permission\Models\Permission::where('name', 'like', 'manage %')->get();
foreach ($managePerms as $perm) {
    $viewName = str_replace('manage', 'view', $perm->name);
    \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $viewName, 'guard_name' => 'admin']);
}
echo "Permissions seeded.\n";
