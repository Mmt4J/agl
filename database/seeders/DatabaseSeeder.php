<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ServiceSeeder::class,
            PricingSeeder::class,
            PortfolioSeeder::class,
            BlogSeeder::class,
            TestimonialSeeder::class,
            FaqSeeder::class,
            DeviceIndustrySeeder::class,
            RepairPricingSeeder::class,
            BusinessHourSeeder::class,
            SettingSeeder::class,
        ]);
    }
}
