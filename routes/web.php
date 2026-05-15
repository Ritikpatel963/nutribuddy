<?php

use App\Http\Controllers\CsrfTokenController;
use App\Http\Controllers\StorageController;
use Illuminate\Support\Facades\Route;

Route::get('/csrf-token', [CsrfTokenController::class, 'show'])->name('csrf.token');

Route::get('/storage/{path}', [StorageController::class, 'showPublic'])
    ->where('path', '.*')
    ->name('storage.public');

require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/storefront.php';
require __DIR__ . '/user.php';
