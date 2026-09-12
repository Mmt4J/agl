<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

it('shows the quote CTA to a guest', function () {
    actingAs(User::factory()->create())
        ->get(route('website.home'))
        ->assertOk()
        ->assertSee(route('website.quote'))
        ->assertDontSee(route('admin.overview'));
});

it('shows the admin link and admin first name to an admin', function () {
    $admin = User::factory()->create(['name' => 'Tolu Admin', 'role' => 'admin']);

    actingAs($admin)
        ->get(route('website.home'))
        ->assertOk()
        ->assertSee('Tolu')
        ->assertSee(route('admin.overview'));
});
