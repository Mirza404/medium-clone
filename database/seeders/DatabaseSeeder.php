<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Admin User',
                'username' => 'admin',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );

        User::factory(10)->create();

        $categories = ['Technology', 'Health', 'Lifestyle', 'Education', 'Travel', 'Sports'];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category]);
        }

        $this->call(PostSeeder::class);

    }
}
