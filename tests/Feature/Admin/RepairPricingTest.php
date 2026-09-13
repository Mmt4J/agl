<?php

use App\Models\RepairDeviceType;
use App\Models\RepairIssueType;
use App\Models\RepairPricing;
use App\Models\User;

it('renders the pricing matrix as per-device cards', function () {
    $phone = RepairDeviceType::create(['name' => 'Smartphone']);
    $laptop = RepairDeviceType::create(['name' => 'Laptop']);

    $cracked = RepairIssueType::create(['name' => 'Cracked screen']);
    $battery = RepairIssueType::create(['name' => 'Battery']);
    $usb = RepairIssueType::create(['name' => 'USB port']);

    RepairPricing::create([
        'repair_device_type_id' => $phone->id,
        'repair_issue_type_id' => $cracked->id,
        'price_min' => 15000,
        'price_max' => 45000,
    ]);

    $this->actingAs(User::factory()->create())
        ->get(route('admin.reference.repair-pricing'))
        ->assertOk()
        ->assertSee('Pricing matrix')
        ->assertSee('Smartphone')
        ->assertSee('Laptop')
        ->assertSee('Cracked screen')
        ->assertSee('₦15,000 – ₦45,000')
        ->assertSee('Set price')
        ->assertSee('1 / 3 priced');
});
