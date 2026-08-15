<?php

namespace Database\Seeders;

use App\Models\PricingCategory;
use Illuminate\Database\Seeder;

class PricingSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'tech' => ['name' => 'Web & Software', 'slug' => 'web-software', 'sort_order' => 1, 'plans' => [
                ['name' => 'Starter Site', 'tagline' => 'A focused site for a small business', 'price_label' => '₦180,000', 'period_label' => 'one-time', 'is_highlighted' => false,
                    'features' => ['Up to 5 pages', 'Mobile-responsive design', 'Basic SEO setup', '2 rounds of revisions']],
                ['name' => 'Business App', 'tagline' => 'Custom web or mobile application', 'price_label' => '₦650,000', 'period_label' => 'starting from', 'is_highlighted' => true,
                    'features' => ['Custom feature set', 'Admin dashboard', 'API integrations', '3 months support included']],
                ['name' => 'Enterprise Platform', 'tagline' => 'Multi-module software systems', 'price_label' => 'Custom', 'period_label' => 'quoted per scope', 'is_highlighted' => false,
                    'features' => ['Dedicated project team', 'Data migration', 'Staff training', 'Ongoing maintenance plan']],
            ]],
            'repair' => ['name' => 'Electronics Repair', 'slug' => 'electronics-repair', 'sort_order' => 2, 'plans' => [
                ['name' => 'Basic Diagnostic', 'tagline' => 'Assessment before any repair', 'price_label' => '₦3,000', 'period_label' => 'per device', 'is_highlighted' => false,
                    'features' => ['Full diagnostic report', 'No-obligation quote', 'Same-day turnaround', 'Applied to repair cost if you proceed']],
                ['name' => 'Standard Repair', 'tagline' => 'Most common phone/laptop fixes', 'price_label' => '₦15,000+', 'period_label' => 'depending on part', 'is_highlighted' => true,
                    'features' => ['Genuine/OEM parts', '12-week warranty', 'Free diagnostic included', 'Pickup & drop-off available']],
                ['name' => 'Business Fleet Care', 'tagline' => 'Ongoing support for offices', 'price_label' => 'Custom', 'period_label' => 'monthly plan', 'is_highlighted' => false,
                    'features' => ['Priority turnaround', 'On-site visits', 'Multiple device coverage', 'Monthly health checks']],
            ]],
            'branding' => ['name' => 'Branding & Design', 'slug' => 'branding-design', 'sort_order' => 3, 'plans' => [
                ['name' => 'Brand Basics', 'tagline' => 'Logo & starter identity', 'price_label' => '₦80,000', 'period_label' => 'one-time', 'is_highlighted' => false,
                    'features' => ['Logo design (3 concepts)', 'Colour & font system', 'Social media kit', '1 round of revisions']],
                ['name' => 'Full Identity', 'tagline' => 'Complete brand system', 'price_label' => '₦220,000', 'period_label' => 'one-time', 'is_highlighted' => true,
                    'features' => ['Full brand guideline doc', 'Marketing collateral set', 'Packaging design', '3 rounds of revisions']],
                ['name' => 'Ongoing Design', 'tagline' => 'Monthly creative support', 'price_label' => '₦100,000', 'period_label' => 'per month', 'is_highlighted' => false,
                    'features' => ['Unlimited design requests', 'Social media content design', '48-hour turnaround', 'Cancel anytime']],
            ]],
        ];

        foreach ($categories as $cat) {
            $plans = $cat['plans'];
            unset($cat['plans']);

            $category = PricingCategory::create($cat);

            foreach ($plans as $i => $planData) {
                $features = $planData['features'];
                unset($planData['features']);
                $planData['sort_order'] = $i + 1;

                $plan = $category->plans()->create($planData);

                foreach ($features as $j => $feature) {
                    $plan->features()->create(['feature' => $feature, 'sort_order' => $j + 1]);
                }
            }
        }
    }
}
