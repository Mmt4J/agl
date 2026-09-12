<?php

use App\Livewire\Admin\Content\Testimonials;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('saves a testimonial with an image URL', function () {
    Livewire::actingAs(User::factory()->create())
        ->test(Testimonials::class)
        ->set('clientName', 'Adaeze O.')
        ->set('clientRole', 'Boutique Owner, Osogbo')
        ->set('quote', 'They rebuilt our online store in two weeks.')
        ->set('imagePath', 'https://example.com/avatar.png')
        ->set('rating', 5)
        ->set('isApproved', true)
        ->set('sortOrder', 1)
        ->call('save')
        ->assertHasNoErrors();

    $testimonial = Testimonial::first();

    expect($testimonial->image_path)->toBe('https://example.com/avatar.png')
        ->and($testimonial->client_name)->toBe('Adaeze O.');
});

it('rejects an invalid image path', function () {
    Livewire::actingAs(User::factory()->create())
        ->test(Testimonials::class)
        ->set('clientName', 'Adaeze O.')
        ->set('clientRole', 'Boutique Owner')
        ->set('quote', 'Great work.')
        ->set('imagePath', 'not-a-url')
        ->call('save')
        ->assertHasErrors(['imagePath']);

    expect(Testimonial::count())->toBe(0);
});

it('stores an uploaded image on the public disk', function () {
    Storage::fake('public');

    Livewire::actingAs(User::factory()->create())
        ->test(Testimonials::class)
        ->set('clientName', 'Kayode F.')
        ->set('clientRole', 'Office Manager')
        ->set('quote', 'The monthly care plan fixed our laptop fleet.')
        ->set('imageFile', UploadedFile::fake()->image('photo.jpg'))
        ->call('save')
        ->assertHasNoErrors();

    $testimonial = Testimonial::first();

    expect($testimonial->image_path)->toStartWith('testimonials/');
    Storage::disk('public')->assertExists($testimonial->image_path);
});

it('resolves stored paths and absolute URLs for display', function () {
    Storage::fake('public');

    $stored = Testimonial::create([
        'client_name' => 'Stored',
        'client_role' => 'Role',
        'quote' => 'Quote',
        'image_path' => 'testimonials/photo.jpg',
    ]);
    $linked = Testimonial::create([
        'client_name' => 'Linked',
        'client_role' => 'Role',
        'quote' => 'Quote',
        'image_path' => 'https://example.com/avatar.png',
    ]);
    $empty = Testimonial::create([
        'client_name' => 'Empty',
        'client_role' => 'Role',
        'quote' => 'Quote',
    ]);

    expect($stored->imageUrl())->toBe(Storage::disk('public')->url('testimonials/photo.jpg'))
        ->and($linked->imageUrl())->toBe('https://example.com/avatar.png')
        ->and($empty->imageUrl())->toBeNull();
});

it('shows the client image URL on the home page carousel', function () {
    Testimonial::create([
        'client_name' => 'Blessing A.',
        'client_role' => 'First-time land buyer',
        'quote' => 'They walked me through every document.',
        'image_path' => 'https://example.com/client.jpg',
        'is_approved' => true,
        'sort_order' => 1,
    ]);

    actingAs(User::factory()->create())
        ->get(route('website.home'))
        ->assertOk()
        ->assertSee('Blessing A.')
        ->assertSee('example.com')
        ->assertSee('client.jpg');
});
