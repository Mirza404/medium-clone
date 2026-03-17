<?php

use App\Models\Category;
use App\Models\Clap;
use App\Models\Post;
use App\Models\User;

it('belongs to both a post and a user', function () {
    $user = User::factory()->create();
    $category = Category::create(['name' => 'Design']);

    $post = Post::factory()
        ->for($user)
        ->state(['category_id' => $category->id])
        ->create();

    $clap = Clap::create([
        'post_id' => $post->id,
        'user_id' => $user->id,
    ]);

    expect($clap->post->is($post))->toBeTrue()
        ->and($clap->user->is($user))->toBeTrue();
});
