<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('dashboard');

// Route::get('/', function () {
//     return Inertia::render('welcome');
// })->name('home');

// Route::get('/test', function (Illuminate\Http\Request $request) {
//     return [
//         'method' => $request->method(),
//         'url' => $request->url(),
//         'path' => $request->path(),
//         'fullUrl' => $request->fullUrl(),
//         'ip' => $request->ip(),
//         'userAgent' => $request->userAgent(),
//         'header' => $request->header(),
//     ];
// });

// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::get('dashboard', function () {
//         return Inertia::render('dashboard');
//     })->name('dashboard');
// });

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
