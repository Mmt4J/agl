<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            ['client_name' => 'Adaeze O.', 'client_role' => 'Boutique Owner, Osogbo', 'quote' => 'They rebuilt our online store in two weeks and it has genuinely changed how customers find us.'],
            ['client_name' => 'Kayode F.', 'client_role' => 'Office Manager', 'quote' => 'Our laptop fleet was breaking down constantly. Their monthly care plan fixed that headache completely.'],
            ['client_name' => 'Blessing A.', 'client_role' => 'First-time land buyer', 'quote' => 'They walked me through every document before I paid a kobo. I felt genuinely protected.'],
            ['client_name' => 'Tunde S.', 'client_role' => 'Startup Founder', 'quote' => 'The consulting session alone saved us from a very expensive software mistake.'],
            ['client_name' => 'Funmilayo R.', 'client_role' => 'Event Planner', 'quote' => 'The Ankara pieces they made for our team were sharp, on time, and exactly to spec.'],
        ];

        foreach ($testimonials as $i => $t) {
            Testimonial::create($t + ['rating' => 5, 'is_approved' => true, 'sort_order' => $i + 1]);
        }
    }
}
