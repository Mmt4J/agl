<?php

namespace Database\Seeders;

use App\Models\BusinessHour;
use Illuminate\Database\Seeder;

class BusinessHourSeeder extends Seeder
{
    public function run(): void
    {
        // Carbon: 0 = Sunday ... 6 = Saturday. Mon–Sat, 8:00am–6:00pm; closed Sunday.
        for ($day = 0; $day <= 6; $day++) {
            $isSunday = $day === 0;

            BusinessHour::create([
                'day_of_week' => $day,
                'opens_at' => $isSunday ? null : '08:00:00',
                'closes_at' => $isSunday ? null : '18:00:00',
                'is_closed' => $isSunday,
            ]);
        }
    }
}
