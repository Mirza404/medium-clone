<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;

it('allows an authenticated user to clap and unclap a post', function () {
    $author = User::factory()->create();
    $fan = User::factory()->create();
    $category = Category::create(['name' => 'Stories']);

    $post = Post::factory()
        ->for($author)
        ->state([
            'category_id' => $category->id,
            'published_at' => now()->subDay(),
        ])->create();

    $clapResponse = $this->actingAs($fan)
        ->post(route('clap', $post));

    $clapResponse
        ->assertOk()
        ->assertJson(['clapsCount' => 1]);

    $this->assertDatabaseHas('claps', [
        'post_id' => $post->id,
        'user_id' => $fan->id,
    ]);

    $unclapResponse = $this->actingAs($fan)
        ->post(route('clap', $post));

    $unclapResponse
        ->assertOk()
        ->assertJson(['clapsCount' => 0]);

    $this->assertDatabaseMissing('claps', [
        'post_id' => $post->id,
        'user_id' => $fan->id,
    ]);
});
