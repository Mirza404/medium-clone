<?php

use App\Models\Post;

// Assuming average reading speed of 100 words per minute

test('calculates reading time from post content', function () {
    $post = new Post([
        'content' => str_repeat('word ', 250),
    ]);

    expect($post->readTime())->toBeInt()->toBe(3);
});

test('reading time handles below 100 words', function () {
    $post = new Post([
        'content' => 'AYYY',
    ]);

    expect($post->readTime())->toBeLessThan(1.0);
});

test('reading time ignores html tags', function () {
    $post = new Post([
        'content' => '<p>This is a sample reader</p>',
    ]);

    expect($post->readTime(wordsPerMinute: 2))->toBeInt()->toBe(3);
});
