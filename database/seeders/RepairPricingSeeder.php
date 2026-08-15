<?php

namespace Database\Seeders;

use App\Models\RepairDeviceType;
use App\Models\RepairIssueType;
use App\Models\RepairPricing;
use Illuminate\Database\Seeder;

class RepairPricingSeeder extends Seeder
{
    public function run(): void
    {
        $devices = collect(['Android & iPhone', 'Laptop & MacBook', 'Tablet & iPad', 'Home Appliance'])
            ->mapWithKeys(fn ($name, $i) => [$name => RepairDeviceType::create(['name' => $name, 'sort_order' => $i + 1])]);

        $issues = collect(['Screen damage', 'Battery replacement', 'Charging port', 'Liquid damage', 'Software / OS issue'])
            ->mapWithKeys(fn ($name, $i) => [$name => RepairIssueType::create(['name' => $name, 'sort_order' => $i + 1])]);

        // [device][issue] => [min, max] — ported directly from the prototype's repairPriceMatrix.
        // Deliberately sparse: a missing combination (e.g. Home Appliance + Screen damage)
        // means "not a common combination", matching the front-end's empty-state message.
        $matrix = [
            'Android & iPhone' => [
                'Screen damage' => [15000, 45000], 'Battery replacement' => [8000, 20000],
                'Charging port' => [6000, 15000], 'Liquid damage' => [10000, 40000],
                'Software / OS issue' => [5000, 12000],
            ],
            'Laptop & MacBook' => [
                'Screen damage' => [25000, 90000], 'Battery replacement' => [15000, 35000],
                'Charging port' => [10000, 25000], 'Liquid damage' => [20000, 80000],
                'Software / OS issue' => [7000, 18000],
            ],
            'Tablet & iPad' => [
                'Screen damage' => [18000, 55000], 'Battery replacement' => [10000, 25000],
                'Charging port' => [7000, 16000], 'Liquid damage' => [12000, 45000],
                'Software / OS issue' => [5000, 12000],
            ],
            'Home Appliance' => [
                'Liquid damage' => [8000, 30000],
            ],
        ];

        foreach ($matrix as $deviceName => $issueRows) {
            foreach ($issueRows as $issueName => [$min, $max]) {
                RepairPricing::create([
                    'repair_device_type_id' => $devices[$deviceName]->id,
                    'repair_issue_type_id' => $issues[$issueName]->id,
                    'price_min' => $min,
                    'price_max' => $max,
                ]);
            }
        }
    }
}
