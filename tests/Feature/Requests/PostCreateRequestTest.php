<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Http\UploadedFile;

it('requires the uploaded image to be an actual image file', function () {
    $user = User::factory()->create();
    $category = Category::create(['name' => 'Validation']);

    $response = $this->actingAs($user)
        ->from(route('post.create'))
        ->post(route('post.store'), [
            'title' => 'Invalid upload',
            'content' => 'Test content',
            'category_id' => $category->id,
            'image' => UploadedFile::fake()->create('cover.pdf', 20, 'application/pdf'),
        ]);

    $response
        ->assertRedirect(route('post.create'))
        ->assertSessionHasErrors('image');

    $this->assertDatabaseMissing('posts', ['title' => 'Invalid upload']);
});

it('requires a valid category and other mandatory fields', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->from(route('post.create'))
        ->post(route('post.store'), [
            'title' => '',
            'content' => '',
            'category_id' => 999,
            'image' => fakeImageUpload('cover.jpg'),
        ]);

    $response
        ->assertRedirect(route('post.create'))
        ->assertSessionHasErrors(['title', 'content', 'category_id']);
});
