<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;

it('returns posts that belong to the category', function () {
    $user = User::factory()->create();
    $category = Category::create(['name' => 'Technology']);

    $matchingPosts = Post::factory()
        ->count(2)
        ->for($user)
        ->state(['category_id' => $category->id])
        ->create();

    Post::factory()
        ->for($user)
        ->state(['category_id' => Category::create(['name' => 'Lifestyle'])->id])
        ->create();

    expect($category->posts)
        ->toHaveCount(2)
        ->and($category->posts->pluck('id')->sort()->values()->all())
        ->toMatchArray($matchingPosts->pluck('id')->sort()->values()->all());
});
