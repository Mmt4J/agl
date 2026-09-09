<?php

namespace App\Livewire\Admin\Topbar;

use App\Models\BlogPost;
use App\Models\ContactMessage;
use App\Models\Faq;
use App\Models\NewsletterSubscriber;
use App\Models\PortfolioProject;
use App\Models\PricingPlan;
use App\Models\QuoteRequest;
use App\Models\Service;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * Global admin resource search, rendered inside the topbar.
 *
 * An embedded (layout-less) Livewire component: it never owns a whole
 * page, it just powers the search dropdown a user opens in the header.
 * Search is debounced server-side so keystrokes don't hammer the DB.
 */
class GlobalSearch extends Component
{
    public string $query = '';

    public bool $open = false;

    public array $scopes = [
        'quotes' => QuoteRequest::class,
        'messages' => ContactMessage::class,
        'services' => Service::class,
        'portfolio' => PortfolioProject::class,
        'blog' => BlogPost::class,
        'faqs' => Faq::class,
        'testimonials' => Testimonial::class,
        'plans' => PricingPlan::class,
        'subscribers' => NewsletterSubscriber::class,
        'users' => User::class,
    ];

    // Kept as instance maps so the blade view can stay dumb and just loop.
    private const URLS = [
        'quotes' => 'admin.leads.quote-requests',
        'messages' => 'admin.leads.contact-messages',
        'services' => 'admin.content.services',
        'portfolio' => 'admin.content.portfolio',
        'blog' => 'admin.content.blog',
        'faqs' => 'admin.content.faqs',
        'testimonials' => 'admin.content.testimonials',
        'plans' => 'admin.content.pricing',
        'subscribers' => 'admin.leads.newsletter',
        'users' => 'admin.settings.users',
    ];

    private const LABELS = [
        'quotes' => 'Quote requests',
        'messages' => 'Messages',
        'services' => 'Services',
        'portfolio' => 'Portfolio',
        'blog' => 'Blog posts',
        'faqs' => 'FAQs',
        'testimonials' => 'Testimonials',
        'plans' => 'Pricing plans',
        'subscribers' => 'Newsletter',
        'users' => 'Users',
    ];

    #[Computed]
    public function results(): Collection
    {
        $query = trim($this->query);

        if (mb_strlen($query) < 2) {
            return collect();
        }

        $needle = '%'.addcslashes($query, '%_').'%';

        return collect([
            'quotes' => QuoteRequest::where(function ($q) use ($needle) {
                $q->where('full_name', 'like', $needle)
                    ->orWhere('email', 'like', $needle)
                    ->orWhere('phone', 'like', $needle);
            })->limit(3)->get(['id', 'full_name', 'email', 'phone'])->map(fn ($m) => ['key' => 'quote-'.$m->id, 'label' => $m->full_name, 'meta' => $m->email ?: $m->phone, 'type' => 'quotes']),
            'messages' => ContactMessage::where(function ($q) use ($needle) {
                $q->where('full_name', 'like', $needle)
                    ->orWhere('email', 'like', $needle)
                    ->orWhere('subject', 'like', $needle);
            })->limit(3)->get(['id', 'full_name', 'subject'])->map(fn ($m) => ['key' => 'message-'.$m->id, 'label' => $m->full_name, 'meta' => $m->subject, 'type' => 'messages']),
            'services' => Service::where(function ($q) use ($needle) {
                $q->where('name', 'like', $needle)->orWhere('slug', 'like', $needle);
            })->limit(3)->get(['id', 'name'])->map(fn ($m) => ['key' => 'service-'.$m->id, 'label' => $m->name, 'meta' => 'Service', 'type' => 'services']),
            'portfolio' => PortfolioProject::where('title', 'like', $needle)->limit(3)->get(['id', 'title'])->map(fn ($m) => ['key' => 'portfolio-'.$m->id, 'label' => $m->title, 'meta' => 'Portfolio', 'type' => 'portfolio']),
            'blog' => BlogPost::where('title', 'like', $needle)->limit(3)->get(['id', 'title'])->map(fn ($m) => ['key' => 'blog-'.$m->id, 'label' => $m->title, 'meta' => 'Blog', 'type' => 'blog']),
            'faqs' => Faq::where('question', 'like', $needle)->limit(3)->get(['id', 'question'])->map(fn ($m) => ['key' => 'faq-'.$m->id, 'label' => $m->question, 'meta' => 'FAQ', 'type' => 'faqs']),
            'testimonials' => Testimonial::where('client_name', 'like', $needle)->orWhere('quote', 'like', $needle)->limit(3)->get(['id', 'client_name'])->map(fn ($m) => ['key' => 'testimonial-'.$m->id, 'label' => $m->client_name, 'meta' => 'Testimonial', 'type' => 'testimonials']),
            'plans' => PricingPlan::where('name', 'like', $needle)->limit(3)->get(['id', 'name'])->map(fn ($m) => ['key' => 'plan-'.$m->id, 'label' => $m->name, 'meta' => 'Pricing', 'type' => 'plans']),
            'subscribers' => NewsletterSubscriber::where('email', 'like', $needle)->limit(3)->get(['id', 'email'])->map(fn ($m) => ['key' => 'subscriber-'.$m->id, 'label' => $m->email, 'meta' => 'Newsletter', 'type' => 'subscribers']),
            'users' => User::where(function ($q) use ($needle) {
                $q->where('name', 'like', $needle)->orWhere('email', 'like', $needle);
            })->limit(3)->get(['id', 'name', 'email'])->map(fn ($m) => ['key' => 'user-'.$m->id, 'label' => $m->name, 'meta' => $m->email, 'type' => 'users']),
        ])
            ->filter(fn (Collection $group) => $group->isNotEmpty())
            ->map(fn (Collection $group) => $group->values());
    }

    #[Computed]
    public function hasResults(): bool
    {
        return $this->results->isNotEmpty();
    }

    public function urlFor(string $type): string
    {
        return route(self::URLS[$type]);
    }

    public function labelFor(string $type): string
    {
        return self::LABELS[$type];
    }

    public function openSearch(): void
    {
        $this->open = true;
    }

    public function closeSearch(): void
    {
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.admin.topbar.global-search');
    }
}
