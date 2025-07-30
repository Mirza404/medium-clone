<?php

namespace Database\Seeders;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'Edin@fake.com'
        ]);

        $categories = ['Technology', 'Health', 'Lifestyle', 'Education', 'Travel', 'Sports'];

        foreach ($categories as $category) {
            Category::create(['name' => $category],
            
        );
        }

        Post::factory(50)->create([
           
        ]);

    }
}
