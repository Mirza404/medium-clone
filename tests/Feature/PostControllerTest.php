<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

it('shows dashboard posts from followed users only', function () {
    $viewer = User::factory()->create();
    $category = Category::create(['name' => 'Tech']);
    $followedAuthor = User::factory()->create();
    $stranger = User::factory()->create();

    $viewer->following()->attach($followedAuthor->id);

    $visiblePost = Post::factory()
        ->for($followedAuthor)
        ->state([
            'category_id' => $category->id,
            'published_at' => now()->subDay(),
        ])->create();

    Post::factory()
        ->for($stranger)
        ->state([
            'category_id' => $category->id,
            'published_at' => now()->subDay(),
        ])->create();

    $this->actingAs($viewer)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertViewIs('post.index')
        ->assertViewHas('posts', function ($posts) use ($visiblePost) {
            $ids = collect($posts->items())->pluck('id');

            return $ids->contains($visiblePost->id) && $ids->count() === 1;
        });
});

it('filters posts by category', function () {
    $category = Category::create(['name' => 'Science']);
    $otherCategory = Category::create(['name' => 'Travel']);
    $author = User::factory()->create();

    $matchingPost = Post::factory()
        ->for($author)
        ->state([
            'category_id' => $category->id,
            'published_at' => now()->subDay(),
        ])->create();

    Post::factory()
        ->for($author)
        ->state([
            'category_id' => $otherCategory->id,
            'published_at' => now()->subDay(),
        ])->create();

    $this->get(route('post.byCategory', $category))
        ->assertOk()
        ->assertViewIs('post.index')
        ->assertViewHas('posts', function ($posts) use ($matchingPost) {
            return collect($posts->items())->pluck('id')->contains($matchingPost->id);
        });
});

it('shows the creation form with categories', function () {
    $user = User::factory()->create();
    $category = Category::create(['name' => 'Culture']);

    $this->actingAs($user)
        ->get(route('post.create'))
        ->assertOk()
        ->assertViewIs('post.create')
        ->assertViewHas('categories', fn ($categories) => $categories->contains('id', $category->id));
});

it('stores a post, ensures a unique slug, and queues media processing', function () {
    Queue::fake();
    Storage::fake('public');

    $user = User::factory()->create();
    $category = Category::create(['name' => 'AI']);

    Post::factory()
        ->for($user)
        ->state([
            'slug' => 'duplicated-post',
            'category_id' => $category->id,
            'published_at' => now()->subDay(),
        ])->create();

    $response = $this->actingAs($user)
        ->post(route('post.store'), [
            'title' => 'Duplicated Post',
            'content' => 'Controller coverage',
            'category_id' => $category->id,
            'published_at' => now()->toDateTimeString(),
            'image' => fakeImageUpload('cover.jpg'),
        ]);

    $response->assertRedirect(route('dashboard'));

    $post = Post::where('slug', 'duplicated-post-1')->first();

    expect($post)->not->toBeNull();
    expect($post->user_id)->toBe($user->id);
    expect($post->media)->toHaveCount(1);
});
