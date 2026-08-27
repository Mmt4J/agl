<?php

use App\Livewire\Admin\Overview;
use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use App\Models\QuoteRequest;
use App\Models\User;
use Livewire\Livewire;

it('renders the overview dashboard', function () {
    Livewire::actingAs(User::factory()->create())
        ->test(Overview::class)
        ->assertSee('Overview')
        ->assertSee('No recent activity.');
});

it('shows lead metrics and recent activity', function () {
    QuoteRequest::create(['full_name' => 'Ada Lovelace', 'phone' => '555-0100', 'email' => 'ada@example.com', 'status' => 'won']);
    ContactMessage::create(['full_name' => 'Grace Hopper', 'email' => 'grace@example.com', 'subject' => 'Hello', 'message' => 'Need help.', 'status' => 'unread']);
    NewsletterSubscriber::create(['email' => 'subscriber@example.com', 'status' => 'subscribed']);

    Livewire::actingAs(User::factory()->create())
        ->test(Overview::class)
        ->assertSee('Quote requests')
        ->assertSee('Ada Lovelace')
        ->assertSee('Grace Hopper')
        ->assertSee('Subscribers');
});
