<?php

use App\Models\Post;

test('imageUrl returns null when no image exists', function () {
    $post = new Post;
    $post->image = null;

    expect($post->imageUrl())->toBeNull();
});

test('imageUrl returns a storage URL when image exists', function () {
    $post = new Post;
    $post->image = 'posts/foo.jpg';

    expect($post->imageUrl())->toBeString()->toBe('/storage/posts/foo.jpg');
});
