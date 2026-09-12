<?php

use App\Livewire\Admin\Content\Portfolio;
use App\Models\PortfolioCategory;
use App\Models\PortfolioProject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

function portfolio_category(): PortfolioCategory
{
    return PortfolioCategory::create(['name' => 'Websites', 'slug' => 'websites', 'sort_order' => 1]);
}

function apply_project_fields($component, PortfolioCategory $category)
{
    return $component
        ->set('projectCategoryId', $category->id)
        ->set('projectTitle', 'Custom E-commerce Site')
        ->set('slug', 'custom-ecommerce-site')
        ->set('summary', 'A storefront rebuilt for a local boutique.')
        ->set('body', '')
        ->set('isFeatured', false)
        ->set('projectSortOrder', 1);
}

it('saves a project with an image URL', function () {
    $category = portfolio_category();

    $component = Livewire::actingAs(User::factory()->create())->test(Portfolio::class);

    apply_project_fields($component, $category)
        ->set('imagePath', 'https://example.com/cover.jpg')
        ->call('saveProject')
        ->assertHasNoErrors();

    $project = PortfolioProject::first();

    expect($project->image_path)->toBe('https://example.com/cover.jpg')
        ->and($project->title)->toBe('Custom E-commerce Site');
});

it('stores an uploaded project image on the public disk', function () {
    Storage::fake('public');
    $category = portfolio_category();

    $component = Livewire::actingAs(User::factory()->create())->test(Portfolio::class);

    apply_project_fields($component, $category)
        ->set('imageFile', UploadedFile::fake()->image('cover.jpg')->size(2048))
        ->call('saveProject')
        ->assertHasNoErrors();

    $project = PortfolioProject::first();

    expect($project->image_path)->toStartWith('portfolio/')
        ->and($project->imageUrl())->toBe(Storage::disk('public')->url($project->image_path));
    Storage::disk('public')->assertExists($project->image_path);
});

it('clears the URL when a file is picked and vice versa', function () {
    $category = portfolio_category();
    Storage::fake('public');

    Livewire::actingAs(User::factory()->create())
        ->test(Portfolio::class)
        ->set('imagePath', 'https://example.com/cover.jpg')
        ->set('imageFile', UploadedFile::fake()->image('cover.jpg'))
        ->assertSet('imagePath', '')
        ->set('imagePath', 'https://example.com/cover.jpg')
        ->assertSet('imageFile', null);

    expect(PortfolioProject::count())->toBe(0);
});

it('rejects an invalid image path', function () {
    $category = portfolio_category();

    $component = Livewire::actingAs(User::factory()->create())->test(Portfolio::class);

    apply_project_fields($component, $category)
        ->set('imagePath', 'not-a-valid-value')
        ->call('saveProject')
        ->assertHasErrors(['imagePath']);

    expect(PortfolioProject::count())->toBe(0);
});

it('resolves stored paths, absolute URLs and root paths for display', function () {
    Storage::fake('public');

    $category = portfolio_category();
    $stored = PortfolioProject::create([
        'portfolio_category_id' => $category->id,
        'title' => 'Stored',
        'slug' => 'stored',
        'summary' => 'Summary',
        'image_path' => 'portfolio/photo.jpg',
    ]);
    $linked = PortfolioProject::create([
        'portfolio_category_id' => $category->id,
        'title' => 'Linked',
        'slug' => 'linked',
        'summary' => 'Summary',
        'image_path' => 'https://example.com/cover.jpg',
    ]);
    $root = PortfolioProject::create([
        'portfolio_category_id' => $category->id,
        'title' => 'Root',
        'slug' => 'root',
        'summary' => 'Summary',
        'image_path' => '/uploads/cover.jpg',
    ]);
    $empty = PortfolioProject::create([
        'portfolio_category_id' => $category->id,
        'title' => 'Empty',
        'slug' => 'empty',
        'summary' => 'Summary',
    ]);

    expect($stored->imageUrl())->toBe(Storage::disk('public')->url('portfolio/photo.jpg'))
        ->and($linked->imageUrl())->toBe('https://example.com/cover.jpg')
        ->and($root->imageUrl())->toBe('/uploads/cover.jpg')
        ->and($empty->imageUrl())->toBeNull();
});

it('shows the image URL and the placeholder on the portfolio page', function () {
    $category = portfolio_category();
    PortfolioProject::create([
        'portfolio_category_id' => $category->id,
        'title' => 'Wired Boutique',
        'slug' => 'wired-boutique',
        'summary' => 'Summary',
        'image_path' => 'https://example.com/cover.jpg',
    ]);
    PortfolioProject::create([
        'portfolio_category_id' => $category->id,
        'title' => 'Unwired Store',
        'slug' => 'unwired-store',
        'summary' => 'Summary',
    ]);

    actingAs(User::factory()->create())
        ->get(route('website.portfolio'))
        ->assertOk()
        ->assertSee('example.com')
        ->assertSee('cover.jpg')
        ->assertSee('Placeholder image for Unwired Store');
});

it('shows the placeholder for a project without an image on the home page', function () {
    $category = portfolio_category();
    PortfolioProject::create([
        'portfolio_category_id' => $category->id,
        'title' => 'Grace House Rebrand',
        'slug' => 'grace-house-rebrand',
        'summary' => 'Summary',
    ]);

    actingAs(User::factory()->create())
        ->get(route('website.home'))
        ->assertOk()
        ->assertSee('Placeholder image for Grace House Rebrand');
});
