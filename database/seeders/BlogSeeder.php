<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect(['Technology', 'Real Estate', 'Business', 'Fashion'])
            ->mapWithKeys(fn ($name) => [$name => BlogCategory::create(['name' => $name, 'slug' => str($name)->slug()])]);

        $tags = collect(['repairs', 'nigeria', 'smes', 'branding', 'ict', 'property'])
            ->mapWithKeys(fn ($name) => [$name => Tag::create(['name' => $name, 'slug' => $name])]);

        $matthew = User::where('email', 'alabimatthew@gmail.com')->first();
        $samuel = User::where('email', 'alabisamuelc@gmail.com')->first();

        $posts = [
            ['title' => 'How to choose a web developer in Nigeria without getting burned', 'category' => 'Technology', 'author' => $matthew, 'featured' => true,
                'excerpt' => 'Five practical questions to ask before you pay a deposit for any website project.', 'read_time' => 6, 'tags' => ['ict', 'smes']],
            ['title' => 'Repair or replace? A simple decision framework for your phone', 'category' => 'Technology', 'author' => $samuel, 'featured' => false,
                'excerpt' => 'A quick cost-based rule of thumb we use with every customer who walks in.', 'read_time' => 4, 'tags' => ['repairs']],
            ['title' => 'What to check before buying land around Osogbo', 'category' => 'Real Estate', 'author' => $matthew, 'featured' => false,
                'excerpt' => 'Documentation red flags every first-time land buyer in Osun State should know.', 'read_time' => 7, 'tags' => ['property', 'nigeria']],
            ['title' => 'Building a credible brand identity on a small business budget', 'category' => 'Business', 'author' => $samuel, 'featured' => false,
                'excerpt' => 'What actually moves the needle when you cannot afford a full agency package.', 'read_time' => 5, 'tags' => ['branding', 'smes']],
            ['title' => 'Five ICT skills worth learning in 2026', 'category' => 'Technology', 'author' => $matthew, 'featured' => false,
                'excerpt' => 'What we are seeing employers and clients actually ask for this year.', 'read_time' => 5, 'tags' => ['ict']],
            ['title' => "A first-timer's guide to made-to-measure tailoring", 'category' => 'Fashion', 'author' => $samuel, 'featured' => false,
                'excerpt' => 'What to expect at your first fitting, and how to prepare.', 'read_time' => 4, 'tags' => ['nigeria']],
        ];

        foreach ($posts as $p) {
            $post = BlogPost::create([
                'blog_category_id' => $categories[$p['category']]->id,
                'author_id' => $p['author']->id,
                'title' => $p['title'],
                'slug' => str($p['title'])->slug(),
                'excerpt' => $p['excerpt'],
                // Full body content lives in the CMS/Livewire editor; seeded with the excerpt
                // as a placeholder paragraph so the demo data renders end-to-end.
                'body' => $p['excerpt'],
                'is_featured' => $p['featured'],
                'read_time_minutes' => $p['read_time'],
                'status' => 'published',
                'published_at' => now()->subDays(random_int(1, 60)),
            ]);

            $post->tags()->attach(collect($p['tags'])->map(fn ($t) => $tags[$t]->id));
        }
    }
}
