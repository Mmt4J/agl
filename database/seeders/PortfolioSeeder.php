<?php

namespace Database\Seeders;

use App\Models\PortfolioCategory;
use Illuminate\Database\Seeder;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Web & Apps', 'Electronics', 'Real Estate', 'Branding', 'Fashion'];
        $categoryModels = [];
        foreach ($categories as $i => $name) {
            $categoryModels[$name] = PortfolioCategory::create([
                'name' => $name,
                'slug' => str($name)->slug(),
                'sort_order' => $i + 1,
            ]);
        }

        $projects = [
            ['title' => 'Osogbo Boutique E-commerce Site', 'category' => 'Web & Apps', 'summary' => 'A mobile-first storefront with WhatsApp checkout for a local fashion retailer.', 'is_featured' => true],
            ['title' => 'Corporate Fleet Repair Programme', 'category' => 'Electronics', 'summary' => 'Ongoing device maintenance contract for a 40-staff logistics office.', 'is_featured' => true],
            ['title' => 'Ilobu Road Duplex Sale', 'category' => 'Real Estate', 'summary' => 'End-to-end sale facilitation, from listing to documentation, in Osogbo.', 'is_featured' => true],
            ['title' => 'Grace House Rebrand', 'category' => 'Branding', 'summary' => 'Full identity refresh — logo, packaging, and social templates for a retail brand.', 'is_featured' => false],
            ['title' => 'Ankara Capsule Collection', 'category' => 'Fashion', 'summary' => 'A 12-piece made-to-measure collection produced for a private client event.', 'is_featured' => false],
            ['title' => 'Custom Inventory & POS System', 'category' => 'Web & Apps', 'summary' => 'A lightweight point-of-sale and stock system built for a small electronics retailer.', 'is_featured' => false],
        ];

        foreach ($projects as $i => $p) {
            $categoryModels[$p['category']]->projects()->create([
                'title' => $p['title'],
                'slug' => str($p['title'])->slug(),
                'summary' => $p['summary'],
                'is_featured' => $p['is_featured'],
                'sort_order' => $i + 1,
            ]);
        }
    }
}
