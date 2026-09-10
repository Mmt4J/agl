<?php

namespace App\Livewire\Admin\Topbar;

use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use App\Models\QuoteRequest;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
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
 * after the admin's last "mark all read" is surfaced (both the badge count
 * and the tray list apply the same cutoff). No separate notifications table
 * is needed — the source data is the truth and drops off naturally once its
 * status changes (e.g. a message is marked read, a quote is contacted) or
 * the admin marks everything read.
 */
class Notifications extends Component
{
    private const CACHE_PREFIX = 'admin.notifications.last_read.';

    public string $typeFilter = 'all';

    #[Computed]
    public function items(): Collection
    {
        $lastRead = $this->lastReadAt();

        $quotes = $this->unreadQuotes($lastRead)
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
            ]);

        $messages = $this->unreadMessages($lastRead)
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
            ]);

        $subscribers = $this->unreadSubscribers($lastRead)
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

        return $this->unreadQuotes($lastRead)->count()
            + $this->unreadMessages($lastRead)->count()
            + $this->unreadSubscribers($lastRead)->count();
    }

    public function filterByType(string $type): void
    {
        $this->typeFilter = in_array($type, ['all', 'quotes', 'messages', 'subscribers'], true) ? $type : 'all';
    }

    public function markAllRead(): void
    {
        // Store a plain string, never a Carbon/DateTime object: serialized
        // date payloads don't survive unserialization across PHP versions and
        // come back as __PHP_Incomplete_Class, which would crash parse() on
        // the next read (the reported live error was exactly that).
        Cache::forever($this->cacheKey(), now()->toDateTimeString());

        // The action re-renders the component, which immediately drives both
        // the badge (unreadCount) and the tray list (items) back down to
        // empty, since every unread filter compares against the new cutoff.
        $this->dispatch('toast', message: 'Notifications marked as read.');
    }

    private function unreadQuotes(CarbonInterface $lastRead): Builder
    {
        return QuoteRequest::query()
            ->where('created_at', '>', $lastRead)
            ->whereIn('status', ['new', 'contacted']);
    }

    private function unreadMessages(CarbonInterface $lastRead): Builder
    {
        return ContactMessage::query()
            ->where('created_at', '>', $lastRead)
            ->where('status', 'unread');
    }

    private function unreadSubscribers(CarbonInterface $lastRead): Builder
    {
        return NewsletterSubscriber::query()
            ->where(fn (Builder $q) => $q->where('created_at', '>', $lastRead)->orWhere('subscribed_at', '>', $lastRead))
            ->where('status', 'subscribed');
    }

    private function lastReadAt(): CarbonInterface
    {
        $stored = Cache::get($this->cacheKey());

        // Only a string is a valid cutoff. Anything else (a legacy date
        // OBJECT written before the string fix — possibly unserialized as
        // __PHP_Incomplete_Class) is ignored rather than parsed, so a stale
        // or corrupted entry can never crash the component.
        if (is_string($stored) && $stored !== '') {
            return Carbon::parse($stored);
        }

        return now()->subCenturies(1);
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
