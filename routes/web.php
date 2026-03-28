<?php

use App\Http\Controllers\ClapController;
use App\Http\Controllers\FollowerController;
use App\Http\Controllers\LangchainController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\ReadingListController;
use App\Http\Controllers\ReadingListPostController;
use Illuminate\Support\Facades\Route;

Route::get('/@{user}', [PublicProfileController::class, 'show'])
    ->name('profile.show');

Route::get('/', [PostController::class, 'index'])
    ->name('dashboard');
Route::get('/category/{category}', [PostController::class, 'category'])
    ->name('post.byCategory');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/post/create', [PostController::class, 'create'])
        ->name('post.create');
    Route::post('/post', [PostController::class, 'store'])
        ->name('post.store');
    Route::get('/@{username}/{post:slug}', [PostController::class, 'show'])
        ->name('post.show');
    Route::post('/follow/{user}', [FollowerController::class, 'followUnfollow'])
        ->name('follow');
    Route::post('/clap/{post}', [ClapController::class, 'clap'])
        ->name('clap');
    Route::get('/ai', [LangchainController::class, 'create'])
        ->name('ai.create');
    Route::post('/ai/chat', [LangchainController::class, 'chat'])
        ->name('ai.chat');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/reading-lists/create', [ReadingListController::class, 'create'])
        ->name('reading-lists.create');

    Route::post('/reading-lists', [ReadingListController::class, 'store'])
        ->name('reading-lists.store');

    Route::get('/reading-lists/{readingList}', [ReadingListController::class, 'show'])
        ->name('reading-lists.show');

    Route::post('/reading-lists/{readingList}/posts/{post}', [ReadingListPostController::class, 'toggle'])
        ->name('reading-lists.posts.toggle');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__.'/auth.php';
