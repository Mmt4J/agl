<?php

use App\Models\User;

it('persists the admin sidebar and keeps active links styled via wire:current', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('admin.overview'))
        ->assertOk()
        ->assertSee('x-persist="admin-sidenav"', false)
        ->assertSee('wire:navigate:scroll', false)
        ->assertSee('wire:current.exact="admin-nav-active"', false)
        ->assertDontSee('data-current:');
});
