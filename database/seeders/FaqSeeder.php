<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            ['question' => 'Is ANESMAVISA GLOBAL LTD a registered company?', 'answer' => 'Yes. We are incorporated with the Corporate Affairs Commission under RC 9417288, and registered with SCUML under RN 301801345.'],
            ['question' => 'Do you charge for diagnostics before a repair quote?', 'answer' => 'A small diagnostic fee applies, but it is deducted from your final bill if you proceed with the repair.'],
            ['question' => 'Can you handle a project that spans more than one division — e.g. a website plus branding?', 'answer' => 'Yes, this is exactly the kind of work we are built for. One quote request routes to every relevant team.'],
            ['question' => 'Do you work outside Osogbo?', 'answer' => 'Yes. Software, branding and consulting work is delivered remotely nationwide. Electronics repair and property services are focused on Osun State, with exceptions on request.'],
            ['question' => 'How long does a typical website project take?', 'answer' => 'A starter site typically takes 2–3 weeks from signed scope to launch; custom applications vary by complexity.'],
            ['question' => 'What payment methods do you accept?', 'answer' => 'Bank transfer and POS are accepted at our office. Larger projects follow a milestone-based payment schedule.'],
            ['question' => 'Do repairs come with a warranty?', 'answer' => 'Yes, most repairs carry a 12-week workmanship warranty, excluding accidental damage after handover.'],
            ['question' => 'How quickly will you respond to a quote request?', 'answer' => 'We reply to every quote and contact form submission within one business day.'],
        ];

        foreach ($faqs as $i => $f) {
            Faq::create($f + ['is_active' => true, 'sort_order' => $i + 1]);
        }
    }
}
