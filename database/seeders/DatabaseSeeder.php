<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        User::firstOrCreate(
            ['email' => 'alabimatthew@gmail.com'],
            [
                'name' => 'Matthew Ayodele Alabi',
                'password' => Hash::make('admin1989'),
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'alabisamuelc@gmail.com'],
            [
                'name' => 'Samuel Gift Alabi',
                'password' => Hash::make('admin1989'),
                'role' => 'admin',
            ]
        );
    }
}