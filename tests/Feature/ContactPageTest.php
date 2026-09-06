<?php

use App\Models\Setting;
use Livewire\Livewire;

test('contact page renders company settings', function () {
    Setting::create(['key' => 'company.address', 'value' => 'No. 2 Ajisebiyawo Street, Osogbo', 'type' => 'string']);
    Setting::create(['key' => 'company.email', 'value' => 'hello@example.com', 'type' => 'string']);
    Setting::create(['key' => 'company.phone_primary', 'value' => '08117529331', 'type' => 'string']);

    Livewire::test('pages::website.contact')
        ->assertSee('No. 2 Ajisebiyawo Street, Osogbo')
        ->assertSee('hello@example.com')
        ->assertSee('08117529331');
});

test('contact form stores an unread message', function () {
    Livewire::test('pages::website.contact')
        ->set('fullName', 'Ada Lovelace')
        ->set('email', 'ada@example.com')
        ->set('subject', 'Website enquiry')
        ->set('message', 'I would like to discuss a new website.')
        ->call('sendMessage')
        ->assertSet('sent', true)
        ->assertHasNoErrors();

    $this->assertDatabaseHas('contact_messages', [
        'full_name' => 'Ada Lovelace',
        'email' => 'ada@example.com',
        'subject' => 'Website enquiry',
        'status' => 'unread',
    ]);
});
