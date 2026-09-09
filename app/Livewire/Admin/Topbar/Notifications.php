<?php

namespace App\Livewire\Admin\Topbar;

use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use App\Models\QuoteRequest;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * Admin notifications, derived in real time from the lead data that
 * actually exists (new quote requests, unread contact messages, new
 * newsletter subscribers) rather than a static placeholder.
 *
 * Reads are tracked per-admin via a cache timestamp: every item created
 * after the admin's last "mark all read" is surfaced with a badge. No
 * separate notifications table is needed — the source data is the truth
 * and drops off naturally once its status changes (e.g. a message is
 * marked read, a quote is contacted).
 */
class Notifications extends Component
{
    private const CACHE_PREFIX = 'admin.notifications.last_read.';

    public string $typeFilter = 'all';

    #[Computed]
    public function items(): Collection
    {
        $quotes = QuoteRequest::query()
            ->latest()
            ->limit(6)
            ->get(['id', 'full_name', 'email', 'status', 'created_at'])
            ->map(fn (QuoteRequest $q) => [
                'key' => 'quote-'.$q->id,
                'type' => 'quotes',
                'title' => "New quote request from {$q->full_name}",
                'meta' => $q->email,
                'url' => route('admin.leads.quote-requests'),
                'created_at' => $q->created_at,
                'time' => $q->created_at->diffForHumans(),
                'status' => $q->status,
                'actionable' => in_array($q->status, ['new', 'contacted'], true),
            ]);

        $messages = ContactMessage::query()
            ->latest()
            ->limit(6)
            ->get(['id', 'full_name', 'email', 'subject', 'status', 'created_at'])
            ->map(fn (ContactMessage $m) => [
                'key' => 'message-'.$m->id,
                'type' => 'messages',
                'title' => "New message from {$m->full_name}",
                'meta' => $m->subject,
                'url' => route('admin.leads.contact-messages'),
                'created_at' => $m->created_at,
                'time' => $m->created_at->diffForHumans(),
                'status' => $m->status,
                'actionable' => $m->status === 'unread',
            ]);

        $subscribers = NewsletterSubscriber::query()
            ->latest('subscribed_at')
            ->limit(4)
            ->get(['id', 'email', 'status', 'subscribed_at'])
            ->map(fn (NewsletterSubscriber $s) => [
                'key' => 'subscriber-'.$s->id,
                'type' => 'subscribers',
                'title' => 'New newsletter subscriber',
                'meta' => $s->email,
                'url' => route('admin.leads.newsletter'),
                'created_at' => $s->subscribed_at ?? $s->created_at,
                'time' => ($s->subscribed_at ?? $s->created_at)->diffForHumans(),
                'status' => $s->status,
                'actionable' => $s->status === 'subscribed',
            ]);

        $items = $quotes->merge($messages)->merge($subscribers)
            ->sortByDesc('created_at')
            ->values();

        return $this->typeFilter === 'all'
            ? $items
            : $items->where('type', $this->typeFilter)->values();
    }

    #[Computed]
    public function unreadCount(): int
    {
        if (! auth()->check()) {
            return 0;
        }

        $lastRead = $this->lastReadAt();

        return QuoteRequest::where('created_at', '>', $lastRead)->whereIn('status', ['new', 'contacted'])->count()
            + ContactMessage::where('created_at', '>', $lastRead)->where('status', 'unread')->count()
            + NewsletterSubscriber::where(function ($q) use ($lastRead) {
                $q->where('created_at', '>', $lastRead)
                    ->orWhere('subscribed_at', '>', $lastRead);
            })->where('status', 'subscribed')->count();
    }

    public function filterByType(string $type): void
    {
        $this->typeFilter = in_array($type, ['all', 'quotes', 'messages', 'subscribers'], true) ? $type : 'all';
    }

    public function markAllRead(): void
    {
        Cache::forever($this->cacheKey(), now());

        // The action re-renders the component, which immediately drives the
        // badge (unreadCount) back down to zero without a page refresh.
        $this->dispatch('toast', message: 'Notifications marked as read.');
    }

    private function lastReadAt(): CarbonInterface
    {
        $stored = Cache::get($this->cacheKey());

        // The app may be configured with immutable dates, in which case
        // `now()` yields a CarbonImmutable — not an Illuminate\Support\Carbon.
        // The query builder accepts any CarbonInterface, so normalizing to
        // one keeps the unread cutoff comparison reliable either way.
        return $stored ? Carbon::parse($stored) : now()->subCenturies(1);
    }

    private function cacheKey(): string
    {
        return self::CACHE_PREFIX.auth()->id();
    }

    public function render()
    {
        return view('livewire.admin.topbar.notifications');
    }
}
