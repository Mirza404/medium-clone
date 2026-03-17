<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

it('builds avatar urls using the storage facade and returns null when missing', function () {
    Storage::shouldReceive('url')
        ->once()
        ->with('avatars/john.png')
        ->andReturn('http://example.test/storage/avatars/john.png');

    $userWithImage = new User(['image' => 'avatars/john.png']);
    $userWithoutImage = new User;

    expect($userWithImage->imageUrl())->toBe('http://example.test/storage/avatars/john.png');
    expect($userWithoutImage->imageUrl())->toBeNull();
});

it('uses the username as the route key', function () {
    $user = new User;

    expect($user->getRouteKeyName())->toBe('username');
});

it('detects when another user follows them', function () {
    $user = User::factory()->create();
    $follower = User::factory()->create();

    $user->followers()->attach($follower->id);

    expect($user->fresh()->isFollowedBy($follower))->toBeTrue()
        ->and($user->isFollowedBy(null))->toBeFalse();
});

it('knows which posts a user has clapped', function () {
    $author = User::factory()->create();
    $fan = User::factory()->create();
    $category = Category::create(['name' => 'Dev']);

    $post = Post::factory()
        ->for($author)
        ->state(['category_id' => $category->id])
        ->create();

    expect($fan->hasClapped($post))->toBeFalse();

    $post->claps()->create([
        'user_id' => $fan->id,
    ]);

    expect($fan->hasClapped($post))->toBeTrue()
        ->and($fan->hasClapped(null))->toBeFalse();
});
