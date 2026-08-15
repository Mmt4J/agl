<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\Industry;
use Illuminate\Database\Seeder;

class DeviceIndustrySeeder extends Seeder
{
    public function run(): void
    {
        $devices = [
            ['name' => 'Laptops & MacBooks', 'examples' => 'Hardware, OS & performance issues', 'icon' => 'laptop'],
            ['name' => 'Android & iPhones', 'examples' => 'Screen, battery, charging port repairs', 'icon' => 'device-phone'],
            ['name' => 'Tablets & iPads', 'examples' => 'Screen, battery & software troubleshooting', 'icon' => 'device-tablet'],
            ['name' => 'Home Appliances', 'examples' => 'Fans, blenders, small kitchen electronics', 'icon' => 'appliance'],
            ['name' => 'Office Electronics', 'examples' => 'Printers, routers, projectors', 'icon' => 'printer'],
            ['name' => 'Networking Equipment', 'examples' => 'Routers, switches, Wi-Fi setup', 'icon' => 'wifi'],
            ['name' => 'POS & Payment Devices', 'examples' => 'POS terminals, card readers', 'icon' => 'credit-card'],
        ];
        foreach ($devices as $i => $d) {
            Device::create($d + ['sort_order' => $i + 1]);
        }

        $industries = [
            ['name' => 'Real Estate & Property', 'description' => 'Buyers, sellers and developers across Osun State.'],
            ['name' => 'Retail & Fashion', 'description' => 'Boutiques and independent fashion brands.'],
            ['name' => 'SMEs & Startups', 'description' => 'Growing businesses that need software and branding.'],
            ['name' => 'Education & Training', 'description' => 'Schools and centres needing ICT programmes.'],
            ['name' => 'Corporate Offices', 'description' => 'Office electronics, networking and consulting.'],
            ['name' => 'Individuals & Families', 'description' => 'Personal device repairs, tailoring and tech advice.'],
        ];
        foreach ($industries as $i => $ind) {
            Industry::create($ind + ['sort_order' => $i + 1]);
        }
    }
}
