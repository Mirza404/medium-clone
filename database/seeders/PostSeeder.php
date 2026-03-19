<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $categories = Category::all();

        if ($users->isEmpty() || $categories->isEmpty()) {
            $this->command?->warn('Seed users and categories before running the PostSeeder.');

            return;
        }

        $faker = fake();

        $users->each(function (User $user) use ($categories, $faker) {
            $postCount = $user->email === 'test@example.com' ? 5 : random_int(2, 4);

            for ($i = 0; $i < $postCount; $i++) {
                $title = $faker->sentence();

                Post::create([
                    'title' => $title,
                    'slug' => Str::slug($title).'-'.Str::random(6),
                    'content' => $faker->paragraphs(random_int(3, 7), true),
                    'user_id' => $user->id,
                    'category_id' => $categories->random()->id,
                    'published_at' => $faker->boolean(75) ? $faker->dateTimeBetween('-9 months', 'now') : null,
                ]);
            }
        });
    }
}
