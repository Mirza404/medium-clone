<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
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

        // Create 25 random posts
        for ($i = 0; $i < 25; $i++) {
            $title = fake()->sentence();
            Post::create([
                'title' => $title,
                'slug' => Str::slug($title),
                'content' => fake()->paragraphs(rand(3, 8), true),
                'user_id' => fake()->randomElement($userIds),
                'category_id' => fake()->randomElement($categoryIds),
                // Use a placeholder image from an online service
                // Note: This URL will not persist if you don't save the image locally
                // but it's a good way to have a visual reference in development.
                'image' => 'https://picsum.photos/seed/' . rand(100, 999) . '/800/600',
                'published_at' => fake()->dateTimeBetween('-1 year', 'now'),
            ]);
        }
    }
}

