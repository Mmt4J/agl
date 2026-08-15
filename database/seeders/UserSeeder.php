<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Filler accounts for testing lists/pagination in the admin - random
        // fake data is appropriate here, unlike the business content seeders.
        User::factory(10)->create();

        // firstOrCreate: safe to re-run without resetting an account you've
        // already configured (password, 2FA) through the real /register flow.
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
