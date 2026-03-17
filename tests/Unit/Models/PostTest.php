<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

it('calculates read time by rounding up to the nearest minute', function () {
    $post = new Post;
    $post->content = str_repeat('word ', 250);

    expect($post->readTime())->toEqual(3);

    $post->content = 'short text';

    expect($post->readTime())->toEqual(1);
});

it('returns storage url when an image exists and null otherwise', function () {
    Storage::shouldReceive('url')
        ->once()
        ->with('posts/foo.jpg')
        ->andReturn('http://example.test/storage/posts/foo.jpg');

    $postWithImage = new Post;
    $postWithImage->image = 'posts/foo.jpg';
    $postWithoutImage = new Post;

    expect($postWithImage->imageUrl())->toBe('http://example.test/storage/posts/foo.jpg');
    expect($postWithoutImage->imageUrl())->toBeNull();
});

it('returns associated claps for the post', function () {
    $author = User::factory()->create();
    $category = Category::create(['name' => 'News']);
    $post = Post::factory()
        ->for($author)
        ->state(['category_id' => $category->id])
        ->create();

    $fan = User::factory()->create();
    $post->claps()->create([
        'user_id' => $fan->id,
    ]);

    expect($post->claps)
        ->toHaveCount(1)
        ->and($post->claps->first()->user_id)
        ->toBe($fan->id);
});
