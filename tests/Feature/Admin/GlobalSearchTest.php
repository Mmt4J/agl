<?php

use App\Livewire\Admin\Topbar\GlobalSearch;
use App\Models\ContactMessage;
use App\Models\QuoteRequest;
use App\Models\Service;
use App\Models\User;
use Livewire\Livewire;

it('shows a prompt before any query is typed', function () {
    Livewire::actingAs(User::factory()->create())
        ->test(GlobalSearch::class)
        ->assertSee('Type to search across services, quotes, messages & more…');
});

it('suggests quote requests and messages matching the query', function () {
    QuoteRequest::create(['full_name' => 'Ada Lovelace', 'phone' => '555-0100', 'email' => 'ada@example.com']);
    ContactMessage::create(['full_name' => 'Grace Hopper', 'email' => 'grace@example.com', 'subject' => 'Quote help', 'message' => 'Body.', 'status' => 'unread']);

    Livewire::actingAs(User::factory()->create())
        ->test(GlobalSearch::class)
        ->set('query', 'example')
        ->assertSee('Ada Lovelace')
        ->assertSee('Grace Hopper')
        ->assertSee('Quote requests')
        ->assertSee('Messages');
});

it('suggests the right resource section and excludes unrelated results', function () {
    Service::factory()->create(['name' => 'Phone Repair', 'slug' => 'phone-repair']);
    ContactMessage::create(['full_name' => 'Beta', 'email' => 'beta@example.com', 'subject' => 'Welcome', 'message' => 'How do I start.', 'status' => 'unread']);

    Livewire::actingAs(User::factory()->create())
        ->test(GlobalSearch::class)
        ->set('query', 'phone')
        ->assertSee('Phone Repair')
        ->assertSee('Services')
        ->assertDontSee('Beta');
});

it('shows an empty state when nothing matches', function () {
    Livewire::actingAs(User::factory()->create())
        ->test(GlobalSearch::class)
        ->set('query', 'zzz-nothing')
        ->assertSee('No matches');
});
