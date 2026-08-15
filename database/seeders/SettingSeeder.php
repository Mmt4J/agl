<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'company.name' => ['ANESMAVISA GLOBAL LTD', 'string'],
            'company.rc_number' => ['9417288', 'string'],
            'company.scuml_number' => ['301801345', 'string'],
            'company.tin' => ['2622995205241', 'string'],
            'company.incorporated_at' => ['2026-03-15', 'string'],
            'company.address' => ['No. 2 Ajisebiyawo Street, Off Ilobu Road, Service Area, Osogbo, Osun State, Nigeria', 'string'],
            'company.email' => ['anesmavsa@gmail.com', 'string'],
            'company.phone_primary' => ['08117529331', 'string'],
            'company.phone_secondary' => ['07031541267', 'string'],
            'company.whatsapp_number' => ['2348117529331', 'string'],
            'company.whatsapp_default_message' => ["Hello ANESMAVISA GLOBAL LTD, I'd like to enquire about a service.", 'string'],
        ];

        foreach ($settings as $key => [$value, $type]) {
            Setting::create(['key' => $key, 'value' => $value, 'type' => $type]);
        }
    }
}
