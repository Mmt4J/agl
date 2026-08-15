<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'code' => 'A.1', 'name' => 'Electronics Repairs & Sales', 'slug' => 'electronics-repairs',
                'short_description' => 'Phone, laptop and appliance repair with genuine parts.',
                'blurb' => 'Repairs and sales for phones, laptops, and home appliances — genuine parts, honest diagnostics.',
                'description' => 'From cracked screens to dead motherboards, we diagnose and repair consumer electronics — and sell verified new and fairly-used devices.',
                'icon' => 'device-phone', 'sort_order' => 1,
                'features' => ['Android & iPhone repair', 'Laptop & MacBook servicing', 'Tablet & iPad repair', 'Home appliance repair', 'New & fairly-used device sales', '12-week workmanship warranty'],
            ],
            [
                'code' => 'A.2', 'name' => 'Software Development', 'slug' => 'software-development',
                'short_description' => 'Custom-built software for real business workflows.',
                'blurb' => 'Custom software built for real Nigerian business workflows, from POS systems to internal tools.',
                'description' => 'We design and build bespoke software — from inventory and POS systems to internal admin tools — tailored to how your business actually runs.',
                'icon' => 'code-bracket', 'sort_order' => 2,
                'features' => ['Requirements & workflow mapping', 'Custom desktop/web software', 'API integrations', 'Ongoing maintenance plans', 'Data migration support', 'Staff onboarding & training'],
            ],
            [
                'code' => 'A.3', 'name' => 'Website & Mobile App Development', 'slug' => 'web-mobile-development',
                'short_description' => 'Fast, modern sites and apps that convert visitors.',
                'blurb' => 'Websites and mobile apps designed to convert, built on modern, maintainable stacks.',
                'description' => 'We design and develop responsive websites and mobile applications — from business landing pages to full e-commerce and booking platforms.',
                'icon' => 'globe-alt', 'sort_order' => 3,
                'features' => ['Custom website design', 'iOS & Android app development', 'E-commerce storefronts', 'SEO-friendly builds', 'Hosting & domain setup', 'Post-launch support'],
            ],
            [
                'code' => 'A.4', 'name' => 'Real Estate', 'slug' => 'real-estate',
                'short_description' => 'Property sourcing, sales and development advisory.',
                'blurb' => 'Property sourcing, sales facilitation and development advisory across Osun State and beyond.',
                'description' => 'We help clients buy, sell and develop property across Osun State, with due diligence support and connections to verified agents and artisans.',
                'icon' => 'home-modern', 'sort_order' => 4,
                'features' => ['Land & property sourcing', 'Sales facilitation', 'Documentation guidance', 'Site inspection visits', 'Development advisory', 'Investment property leads'],
            ],
            [
                'code' => 'A.5', 'name' => 'Tech Consulting', 'slug' => 'tech-consulting',
                'short_description' => 'Clear, practical technology strategy for small teams.',
                'blurb' => 'Practical technology strategy for small businesses that need clarity, not jargon.',
                'description' => 'We advise small businesses and individuals on the right tools, systems and processes to adopt — without unnecessary complexity or cost.',
                'icon' => 'presentation-chart', 'sort_order' => 5,
                'features' => ['Tech stack audits', 'Digital transformation planning', 'Vendor & tool selection', 'Cybersecurity basics review', 'Process automation advice', 'One-off strategy sessions'],
            ],
            [
                'code' => 'A.6', 'name' => 'Tech Training & ICT', 'slug' => 'ict-training',
                'short_description' => 'Hands-on ICT training for individuals and teams.',
                'blurb' => 'Hands-on tech training and ICT upskilling for individuals, schools and teams.',
                'description' => 'Practical, hands-on ICT training covering computer literacy, office tools, basic programming and digital skills for jobs and business.',
                'icon' => 'academic-cap', 'sort_order' => 6,
                'features' => ['Computer literacy classes', 'Office productivity training', 'Intro to programming', 'Digital skills for entrepreneurs', 'Corporate group training', 'Certificates on completion'],
            ],
            [
                'code' => 'A.7', 'name' => 'Digital Branding & Graphic Design', 'slug' => 'digital-branding',
                'short_description' => 'Brand identities and visual design that build trust.',
                'blurb' => 'Digital branding, graphic design and fashion design that make small businesses look established.',
                'description' => 'Logo design, brand guidelines, social media kits and marketing collateral that make small businesses look established and credible.',
                'icon' => 'paint-brush', 'sort_order' => 7,
                'features' => ['Logo & brand identity', 'Social media design kits', 'Print & marketing materials', 'Brand guideline documents', 'Packaging design', 'Content design support'],
            ],
            [
                'code' => 'A.8', 'name' => 'Fashion Design', 'slug' => 'fashion-design',
                'short_description' => 'Custom tailoring and fashion design services.',
                'blurb' => 'Bespoke tailoring and small-batch fashion production for personal and brand clients.',
                'description' => 'Bespoke fashion design and tailoring — from made-to-measure outfits to small-batch production for personal and brand clients.',
                'icon' => 'scissors', 'sort_order' => 8,
                'features' => ['Made-to-measure outfits', 'Corporate & event wear', 'Small-batch production', 'Fabric sourcing guidance', 'Fittings & alterations', 'Custom brand apparel'],
            ],
        ];

        foreach ($services as $data) {
            $features = $data['features'];
            unset($data['features']);

            $service = Service::create($data);

            foreach ($features as $i => $feature) {
                $service->features()->create(['feature' => $feature, 'sort_order' => $i + 1]);
            }
        }
    }
}
