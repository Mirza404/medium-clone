<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch all user and category IDs to assign posts to them
        $userIds = User::pluck('id');
        $categoryIds = Category::pluck('id');

        // Check if we have users and categories to work with
        if ($userIds->isEmpty() || $categoryIds->isEmpty()) {
            echo "Please run the UserSeeder and CategorySeeder first.\n";

            return;
        }

        /** @var Filesystem $disk */
        $disk = Storage::disk('public');
        $disk->makeDirectory('posts');

        // Create 25 random posts with locally generated cover art
        for ($i = 0; $i < 25; $i++) {
            $title = fake()->sentence(rand(3, 7));
            $post = Post::create([
                'title' => $title,
                'slug' => Str::slug($title.'-'.Str::random(6)),
                'content' => fake()->paragraphs(rand(3, 8), true),
                'user_id' => fake()->randomElement($userIds),
                'category_id' => fake()->randomElement($categoryIds),
                'published_at' => fake()->dateTimeBetween('-1 year', 'now'),
            ]);

            $post->forceFill([
                'image' => $this->createPlaceholderImage($disk, $title),
            ])->save();
        }
    }

    /**
     * Persist a simple SVG cover and return the relative storage path.
     */
    private function createPlaceholderImage(Filesystem $disk, string $title): string
    {
        $fileName = 'posts/'.Str::uuid().'.svg';
        $palette = ['#1d4ed8', '#0f766e', '#9333ea', '#ea580c', '#047857', '#6366f1'];
        $background = $palette[array_rand($palette)];
        $accent = '#f8fafc';
        $muted = '#ffffff20';

        $text = Str::upper(Str::words($title, 3, ''));
        $text = preg_replace('/[^A-Z0-9 ]/', '', $text) ?: 'EVERBIT';

        $svg = <<<SVG
<?xml version="1.0" encoding="UTF-8"?>
<svg width="640" height="480" viewBox="0 0 640 480" xmlns="http://www.w3.org/2000/svg">
    <rect width="640" height="480" fill="{$background}" rx="32" />
    <rect width="520" height="120" x="60" y="70" fill="{$muted}" rx="60" />
    <rect width="420" height="120" x="110" y="220" fill="{$muted}" rx="60" />
    <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle"
        font-family="Inter, 'Helvetica Neue', Arial, sans-serif" font-weight="600"
        font-size="42" fill="{$accent}">{$text}</text>
</svg>
SVG;

        $disk->put($fileName, $svg);

        return $fileName;
    }
}
