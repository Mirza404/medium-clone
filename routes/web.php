<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\FollowerController;
use App\Http\Controllers\ClapController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/@{user}', [PublicProfileController::class, 'show'])
    ->name('profile.show');

Route::get('/deez', function () {
    return inertia('Home');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [PostController::class, 'index'])
        ->name('dashboard');
    Route::get('/category/{category}', [PostController::class, 'category'])
        ->name('post.byCategory');
    Route::get('/post/create', [PostController::class, 'create'])
        ->name('post.create');
    Route::post('/post', [PostController::class, 'store'])
        ->name('post.store');
    Route::get('/@{username}/{post:slug}', [PostController::class, 'show'])
        ->name('post.show');});
    Route::post('/follow/{user}', [FollowerController::class, 'followUnfollow'])
        ->name('follow');
    Route::post('/clap/{post}', [ClapController::class, 'clap'])
        ->name('clap');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__ . '/auth.php';
