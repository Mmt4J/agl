<?php

use App\Livewire\Admin\Topbar\Notifications;
use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use App\Models\QuoteRequest;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

it('shows an empty state when there is no activity', function () {
    Livewire::actingAs(User::factory()->create())
        ->test(Notifications::class)
        ->assertSee('No notifications');
});

it('surfaces real quote requests, messages and subscribers', function () {
    QuoteRequest::create(['full_name' => 'Ada Lovelace', 'phone' => '555-0100', 'email' => 'ada@example.com']);
    ContactMessage::create(['full_name' => 'Grace Hopper', 'email' => 'grace@example.com', 'subject' => 'Hello', 'message' => 'Body.', 'status' => 'unread']);
    NewsletterSubscriber::create(['email' => 'subscriber@example.com', 'status' => 'subscribed', 'subscribed_at' => now()]);

    Livewire::actingAs(User::factory()->create())
        ->test(Notifications::class)
        ->assertSee('New quote request from Ada Lovelace')
        ->assertSee('New message from Grace Hopper')
        ->assertSee('New newsletter subscriber');
});

it('counts unread and new items towards the badge', function () {
    QuoteRequest::create(['full_name' => 'Ada Lovelace', 'phone' => '555-0100', 'email' => 'ada@example.com']);
    ContactMessage::create(['full_name' => 'Grace Hopper', 'email' => 'grace@example.com', 'subject' => 'Hello', 'message' => 'Body.', 'status' => 'unread']);

    $component = Livewire::actingAs(User::factory()->create())->test(Notifications::class);

    expect($component->get('unreadCount'))->toBe(2);
});

it('filters notifications by type', function () {
    QuoteRequest::create(['full_name' => 'Ada Lovelace', 'phone' => '555-0100', 'email' => 'ada@example.com']);
    ContactMessage::create(['full_name' => 'Grace Hopper', 'email' => 'grace@example.com', 'subject' => 'Hello', 'message' => 'Body.', 'status' => 'unread']);

    Livewire::actingAs(User::factory()->create())
        ->test(Notifications::class)
        ->call('filterByType', 'messages')
        ->assertSee('New message from Grace Hopper')
        ->assertDontSee('New quote request from Ada Lovelace');
});

it('marking all read clears the unread badge', function () {
    QuoteRequest::create(['full_name' => 'Ada Lovelace', 'phone' => '555-0100', 'email' => 'ada@example.com', 'status' => 'new']);

    $component = Livewire::actingAs(User::factory()->create())->test(Notifications::class);
    $component->call('markAllRead');

    expect($component->get('unreadCount'))->toBe(0);
});

it('marking all read empties the notification tray', function () {
    QuoteRequest::create(['full_name' => 'Ada Lovelace', 'phone' => '555-0100', 'email' => 'ada@example.com', 'status' => 'new']);

    $component = Livewire::actingAs(User::factory()->create())->test(Notifications::class);
    $component->call('markAllRead');

    $component
        ->assertDontSee('New quote request from Ada Lovelace')
        ->assertSee('No notifications.');
});

it('a new lead appears again after marking all read', function () {
    $user = User::factory()->create();

    QuoteRequest::create(['full_name' => 'First', 'phone' => '555-0100', 'email' => 'first@example.com', 'status' => 'new']);

    Livewire::actingAs($user)
        ->test(Notifications::class)
        ->call('markAllRead');

    $lead = QuoteRequest::create(['full_name' => 'Second', 'phone' => '555-0101', 'email' => 'second@example.com', 'status' => 'new']);
    $lead->forceFill(['created_at' => now()->addSeconds(2)])->save();

    Livewire::actingAs($user)
        ->test(Notifications::class)
        ->assertSee('New quote request from Second')
        ->assertDontSee('New quote request from First');
});

it('survives a corrupted or legacy cache entry', function () {
    $user = User::factory()->create();

    Cache::forever('admin.notifications.last_read.'.$user->id, new stdClass);

    QuoteRequest::create(['full_name' => 'Ada Lovelace', 'phone' => '555-0100', 'email' => 'ada@example.com', 'status' => 'new']);

    Livewire::actingAs($user)
        ->test(Notifications::class)
        ->assertSee('New quote request from Ada Lovelace');
});
