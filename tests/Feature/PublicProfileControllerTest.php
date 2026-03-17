<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;

it('shows a public profile with the user posts', function () {
    $user = User::factory()->create();
    $category = Category::create(['name' => 'Public']);

    $posts = Post::factory()
        ->count(2)
        ->for($user)
        ->state([
            'category_id' => $category->id,
            'published_at' => now()->subDay(),
        ])->create();

    $this->get(route('profile.show', ['user' => $user]))
        ->assertOk()
        ->assertViewIs('profile.show')
        ->assertViewHas('user', fn ($viewUser) => $viewUser->is($user))
        ->assertViewHas('posts', function ($postsPaginator) use ($posts) {
            $ids = collect($postsPaginator->items())->pluck('id')->sort()->values()->all();

            return $ids === $posts->pluck('id')->sort()->values()->all();
        });
});
