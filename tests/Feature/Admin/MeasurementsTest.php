<?php

use App\Livewire\Admin\Measurements\RecordMeasurements;
use App\Livewire\Admin\Measurements\Records;
use App\Models\CustomerMeasurement;
use App\Models\User;
use Livewire\Livewire;

it('renders the record measurements page', function () {
    Livewire::actingAs(User::factory()->create())
        ->test(RecordMeasurements::class)
        ->assertSee('Find an existing customer')
        ->assertSee('Upper body')
        ->assertSee('Lower body')
        ->assertSee('Overall');
});

it('records a new customer with an auto-assigned unique code', function () {
    Livewire::actingAs(User::factory()->create())
        ->test(RecordMeasurements::class)
        ->set('fullName', 'Chidi Okafor')
        ->set('phone', '0800-123-4567')
        ->set('email', 'chidi@example.com')
        ->set('chest', 40.5)
        ->set('bust', 38)
        ->set('waist', 34)
        ->set('shoulder', 17.5)
        ->set('armLength', 24)
        ->set('topLength', 26)
        ->set('halfBust', 20)
        ->set('halfLength', 24)
        ->set('roundSleeve', 14)
        ->set('lengthSleeve', 22)
        ->set('hip', 42)
        ->set('inseam', 30)
        ->set('thigh', 22)
        ->set('ankle', 9.5)
        ->set('trouserSkirtLength', 36)
        ->set('height', 68)
        ->set('weight', 75)
        ->set('gownLength', 58)
        ->set('dressSize', 'L')
        ->set('notes', 'Fitting for a native dashiki.')
        ->call('save')
        ->assertHasNoErrors();

    $record = CustomerMeasurement::sole();

    expect($record->full_name)->toBe('Chidi Okafor')
        ->and($record->phone)->toBe('0800-123-4567')
        ->and($record->customer_code)->toBe('AGL-CUS-0001')
        ->and((float) $record->chest)->toBe(40.5)
        ->and((float) $record->waist)->toBe(34.0)
        ->and((float) $record->thigh)->toBe(22.0)
        ->and((float) $record->ankle)->toBe(9.5)
        ->and((float) $record->top_length)->toBe(26.0)
        ->and((float) $record->half_bust)->toBe(20.0)
        ->and((float) $record->round_sleeve)->toBe(14.0)
        ->and((float) $record->length_sleeve)->toBe(22.0)
        ->and((float) $record->trouser_skirt_length)->toBe(36.0)
        ->and((float) $record->gown_length)->toBe(58.0)
        ->and($record->dress_size)->toBe('L')
        ->and($record->notes)->toBe('Fitting for a native dashiki.');
});

it('updates the existing record when re-measuring a customer instead of duplicating it', function () {
    $existing = CustomerMeasurement::factory()->create([
        'customer_code' => 'AGL-CUS-0007',
        'full_name' => 'Amina Bello',
        'waist' => 32,
    ]);

    Livewire::actingAs(User::factory()->create())
        ->test(RecordMeasurements::class)
        ->call('selectCustomer', $existing->id)
        ->set('waist', 36)
        ->call('save')
        ->assertHasNoErrors();

    expect(CustomerMeasurement::count())->toBe(1);

    $existing->refresh();

    expect((float) $existing->waist)->toBe(36.0);
});

it('loads an existing record for editing straight from the route', function () {
    $record = CustomerMeasurement::factory()->create(['full_name' => 'Kelechi Nwosu']);

    $this->actingAs(User::factory()->create())
        ->get(route('admin.measurements.record-measurements', ['record' => $record]))
        ->assertOk()
        ->assertSee('Kelechi Nwosu')
        ->assertSee($record->customer_code);
});

it('validates required contact details and caches nothing invalid', function () {
    Livewire::actingAs(User::factory()->create())
        ->test(RecordMeasurements::class)
        ->set('fullName', '')
        ->set('phone', '')
        ->call('save')
        ->assertHasErrors(['fullName', 'phone']);

    expect(CustomerMeasurement::count())->toBe(0);
});

it('rejects negative measurements', function () {
    Livewire::actingAs(User::factory()->create())
        ->test(RecordMeasurements::class)
        ->set('fullName', 'Test Customer')
        ->set('phone', '0800')
        ->set('chest', -5)
        ->call('save')
        ->assertHasErrors(['chest']);

    expect(CustomerMeasurement::count())->toBe(0);
});

it('switches the measurement unit between inches and centimetres', function () {
    Livewire::actingAs(User::factory()->create())
        ->test(RecordMeasurements::class)
        ->set('unit', 'cm')
        ->assertSee('Chest (cm)')
        ->set('unit', 'in')
        ->assertSee('Chest (in)');
});

it('lists measurement records and searches by name, phone and code', function () {
    CustomerMeasurement::factory()->create([
        'customer_code' => 'AGL-CUS-0021',
        'full_name' => 'Amina Bello',
        'phone' => '0901-555-0100',
    ]);
    CustomerMeasurement::factory()->create([
        'customer_code' => 'AGL-CUS-0022',
        'full_name' => 'Tunde Bakare',
        'phone' => '0901-555-0200',
    ]);

    Livewire::actingAs(User::factory()->create())
        ->test(Records::class)
        ->assertSee('Amina Bello')
        ->assertSee('AGL-CUS-0021')
        ->assertSee('Tunde Bakare')
        ->set('search', 'Amina')
        ->assertSee('Amina Bello')
        ->assertDontSee('Tunde Bakare')
        ->set('search', 'AGL-CUS-0022')
        ->assertSee('Tunde Bakare')
        ->assertDontSee('Amina Bello')
        ->set('search', '0901-555-0100')
        ->assertSee('Amina Bello')
        ->assertDontSee('Tunde Bakare');
});

it('shows an empty state when search matches nothing', function () {
    Livewire::actingAs(User::factory()->create())
        ->test(Records::class)
        ->set('search', 'nobody-by-this-name')
        ->assertSee('No records match your search.');
});

it('deletes a measurement record', function () {
    $record = CustomerMeasurement::factory()->create();

    Livewire::actingAs(User::factory()->create())
        ->test(Records::class)
        ->call('confirmDelete', $record->id)
        ->call('deleteConfirmed');

    expect(CustomerMeasurement::count())->toBe(0);
});

it('shows every measurement field on the record page for editing', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('admin.measurements.record-measurements'))
        ->assertOk()
        ->assertSee('Top length')
        ->assertSee('Half bust')
        ->assertSee('Half length')
        ->assertSee('Round sleeve')
        ->assertSee('Sleeve length')
        ->assertSee('Trouser / skirt length')
        ->assertSee('Gown length');
});

it('hides empty measurement values and sections in the record detail view', function () {
    $record = CustomerMeasurement::factory()->create([
        'chest' => 42,
        'waist' => 34,
        'shoulder' => null,
        'arm_length' => null,
        'top_length' => null,
        'half_bust' => null,
        'half_length' => null,
        'round_sleeve' => null,
        'length_sleeve' => null,
        'hip' => null,
        'inseam' => null,
        'thigh' => null,
        'ankle' => null,
        'trouser_skirt_length' => null,
        'height' => null,
        'weight' => null,
        'gown_length' => null,
        'dress_size' => null,
    ]);

    Livewire::actingAs(User::factory()->create())
        ->test(Records::class)
        ->call('viewRecord', $record->id)
        ->assertSee('Upper body')
        ->assertSee('Chest')
        ->assertSee('42.0')
        ->assertSee('Waist')
        ->assertDontSee('Shoulder')
        ->assertDontSee('Sleeve length')
        ->assertDontSee('Lower body')
        ->assertDontSee('Hip')
        ->assertDontSee('Overall')
        ->assertDontSee('Height')
        ->assertDontSee('Gown length');
});

it('serves both measurement admin pages over HTTP', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('admin.measurements.record-measurements'))->assertOk();
    $this->get(route('admin.measurements.records'))->assertOk();
});
