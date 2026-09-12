<?php

use App\Livewire\Admin\Content\Blog;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

function blog_category(): BlogCategory
{
    return BlogCategory::create(['name' => 'Commerce', 'slug' => 'commerce']);
}

function apply_post_fields($component, BlogCategory $category)
{
    return $component
        ->set('blogCategoryId', $category->id)
        ->set('postTitle', 'Notes on Local Commerce')
        ->set('slug', 'notes-on-local-commerce')
        ->set('excerpt', 'Field notes from a Lagos market town.')
        ->set('body', 'Body text.')
        ->set('isFeatured', false)
        ->set('readTimeMinutes', 4)
        ->set('status', 'draft');
}

it('saves a post with an image URL', function () {
    $category = blog_category();

    $component = Livewire::actingAs(User::factory()->create())->test(Blog::class);

    apply_post_fields($component, $category)
        ->set('featuredImage', 'https://example.com/cover.jpg')
        ->call('savePost')
        ->assertHasNoErrors();

    $post = BlogPost::first();

    expect($post->featured_image)->toBe('https://example.com/cover.jpg')
        ->and($post->title)->toBe('Notes on Local Commerce');
});

it('stores an uploaded post image on the public disk', function () {
    Storage::fake('public');
    $category = blog_category();

    $component = Livewire::actingAs(User::factory()->create())->test(Blog::class);

    apply_post_fields($component, $category)
        ->set('imageFile', UploadedFile::fake()->image('cover.jpg')->size(2048))
        ->call('savePost')
        ->assertHasNoErrors();

    $post = BlogPost::first();

    expect($post->featured_image)->toStartWith('blog/')
        ->and($post->imageUrl())->toBe(Storage::disk('public')->url($post->featured_image));
    Storage::disk('public')->assertExists($post->featured_image);
});

it('clears the URL when a file is picked and vice versa', function () {
    $category = blog_category();
    Storage::fake('public');

    Livewire::actingAs(User::factory()->create())
        ->test(Blog::class)
        ->set('featuredImage', 'https://example.com/cover.jpg')
        ->set('imageFile', UploadedFile::fake()->image('cover.jpg'))
        ->assertSet('featuredImage', '')
        ->set('featuredImage', 'https://example.com/cover.jpg')
        ->assertSet('imageFile', null);

    expect(BlogPost::count())->toBe(0);
});

it('rejects an invalid image path', function () {
    $category = blog_category();

    $component = Livewire::actingAs(User::factory()->create())->test(Blog::class);

    apply_post_fields($component, $category)
        ->set('featuredImage', 'not-a-valid-value')
        ->call('savePost')
        ->assertHasErrors(['featuredImage']);

    expect(BlogPost::count())->toBe(0);
});

it('resolves stored paths, absolute URLs and root paths for display', function () {
    Storage::fake('public');

    $category = blog_category();
    $user = User::factory()->create();
    $stored = BlogPost::create([
        'blog_category_id' => $category->id,
        'author_id' => $user->id,
        'title' => 'Stored',
        'slug' => 'stored',
        'excerpt' => 'Excerpt',
        'body' => 'Body',
        'featured_image' => 'blog/photo.jpg',
        'status' => 'draft',
    ]);
    $linked = BlogPost::create([
        'blog_category_id' => $category->id,
        'author_id' => $user->id,
        'title' => 'Linked',
        'slug' => 'linked',
        'excerpt' => 'Excerpt',
        'body' => 'Body',
        'featured_image' => 'https://example.com/cover.jpg',
        'status' => 'draft',
    ]);
    $root = BlogPost::create([
        'blog_category_id' => $category->id,
        'author_id' => $user->id,
        'title' => 'Root',
        'slug' => 'root',
        'excerpt' => 'Excerpt',
        'body' => 'Body',
        'featured_image' => '/uploads/cover.jpg',
        'status' => 'draft',
    ]);
    $empty = BlogPost::create([
        'blog_category_id' => $category->id,
        'author_id' => $user->id,
        'title' => 'Empty',
        'slug' => 'empty',
        'excerpt' => 'Excerpt',
        'body' => 'Body',
        'status' => 'draft',
    ]);

    expect($stored->imageUrl())->toBe(Storage::disk('public')->url('blog/photo.jpg'))
        ->and($linked->imageUrl())->toBe('https://example.com/cover.jpg')
        ->and($root->imageUrl())->toBe('/uploads/cover.jpg')
        ->and($empty->imageUrl())->toBeNull();
});

it('shows the image URL and the placeholder on the blog index page', function () {
    $category = blog_category();
    $user = User::factory()->create();
    BlogPost::create([
        'blog_category_id' => $category->id,
        'author_id' => $user->id,
        'title' => 'Covered Post',
        'slug' => 'covered-post',
        'excerpt' => 'Excerpt',
        'body' => 'Body',
        'featured_image' => 'https://example.com/cover.jpg',
        'status' => 'published',
        'published_at' => now()->subDay(),
    ]);
    BlogPost::create([
        'blog_category_id' => $category->id,
        'author_id' => $user->id,
        'title' => 'Plain Post',
        'slug' => 'plain-post',
        'excerpt' => 'Excerpt',
        'body' => 'Body',
        'status' => 'published',
        'published_at' => now()->subDays(2),
    ]);

    actingAs(User::factory()->create())
        ->get(route('website.blog'))
        ->assertOk()
        ->assertSee('example.com')
        ->assertSee('cover.jpg')
        ->assertSee('Placeholder image for Plain Post');
});

it('shows the image URL and the placeholder on the blog post page', function () {
    $category = blog_category();
    $user = User::factory()->create();
    $covered = BlogPost::create([
        'blog_category_id' => $category->id,
        'author_id' => $user->id,
        'title' => 'Covered Post',
        'slug' => 'covered-post',
        'excerpt' => 'Excerpt',
        'body' => 'Body',
        'featured_image' => 'https://example.com/cover.jpg',
        'status' => 'published',
        'published_at' => now()->subDay(),
    ]);
    $plain = BlogPost::create([
        'blog_category_id' => $category->id,
        'author_id' => $user->id,
        'title' => 'Plain Post',
        'slug' => 'plain-post',
        'excerpt' => 'Excerpt',
        'body' => 'Body',
        'status' => 'published',
        'published_at' => now()->subDays(2),
    ]);

    actingAs(User::factory()->create())
        ->get(route('website.blog.show', $covered))
        ->assertOk()
        ->assertSee('example.com')
        ->assertSee('cover.jpg');

    actingAs(User::factory()->create())
        ->get(route('website.blog.show', $plain))
        ->assertOk()
        ->assertSee('Placeholder image for Plain Post');
});
