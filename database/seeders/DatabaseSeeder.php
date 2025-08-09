<?php

namespace Database\Seeders;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
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
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        User::factory()->create([
            'name' => 'Random User',
            'email' => 'huks@example.com',
            'password' => 'password'
        ]);

        User::factory()->create([
            'name' => 'NonRandom User',
            'email' => 'ruta@example.com',
            'password' => 'password'
        ]);

        $categories = ['Technology', 'Health', 'Lifestyle', 'Education', 'Travel', 'Sports'];

        foreach ($categories as $category) {
            Category::create(['name' => $category],
            
        );
        }

        $this->call(PostSeeder::class);

    }
}
