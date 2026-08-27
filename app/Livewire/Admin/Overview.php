<?php

namespace App\Livewire\Admin;

use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use App\Models\QuoteRequest;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::admin')]
#[Title('Overview')]
class Overview extends Component
{
    #[Computed]
    public function kpis(): array
    {
        $currentPeriod = now()->subDays(6)->startOfDay();
        $previousPeriod = now()->subDays(13)->startOfDay();

        $quoteTrend = $this->trend(
            QuoteRequest::where('created_at', '>=', $currentPeriod)->count(),
            QuoteRequest::whereBetween('created_at', [$previousPeriod, $currentPeriod])->count(),
        );

        $unreadTrend = $this->trend(
            ContactMessage::where('status', 'unread')->where('created_at', '>=', $currentPeriod)->count(),
            ContactMessage::where('status', 'unread')->whereBetween('created_at', [$previousPeriod, $currentPeriod])->count(),
        );

        $subscriberTrend = $this->trend(
            NewsletterSubscriber::where('created_at', '>=', $currentPeriod)->count(),
            NewsletterSubscriber::whereBetween('created_at', [$previousPeriod, $currentPeriod])->count(),
        );

        $wonTrend = $this->trend(
            QuoteRequest::where('status', 'won')->where('created_at', '>=', $currentPeriod)->count(),
            QuoteRequest::where('status', 'won')->whereBetween('created_at', [$previousPeriod, $currentPeriod])->count(),
        );

        return [
            ['label' => 'Quote requests', 'value' => QuoteRequest::count(), 'trend' => $quoteTrend['label'], 'trendUp' => $quoteTrend['increased'], 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75"/>'],
            // Inverted on purpose: fewer unread messages is the good outcome,
            // so "increased" (more unread) should render as the danger color,
            // not sage - the opposite of every other KPI here.
            ['label' => 'Unread messages', 'value' => ContactMessage::where('status', 'unread')->count(), 'trend' => $unreadTrend['label'], 'trendUp' => ! $unreadTrend['increased'], 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75"/>'],
            ['label' => 'Subscribers', 'value' => NewsletterSubscriber::where('status', 'subscribed')->count(), 'trend' => $subscriberTrend['label'], 'trendUp' => $subscriberTrend['increased'], 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912"/>'],
            ['label' => 'Won quotes', 'value' => QuoteRequest::where('status', 'won')->count(), 'trend' => $wonTrend['label'], 'trendUp' => $wonTrend['increased'], 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75"/>'],
        ];
    }

    #[Computed]
    public function leadsLast7Days(): array
    {
        $start = now()->subDays(6)->startOfDay();
        $end = now()->endOfDay();
        $quoteCounts = QuoteRequest::query()->whereBetween('created_at', [$start, $end])->selectRaw('DATE(created_at) as day, COUNT(*) as count')->groupBy('day')->pluck('count', 'day');
        $messageCounts = ContactMessage::query()->whereBetween('created_at', [$start, $end])->selectRaw('DATE(created_at) as day, COUNT(*) as count')->groupBy('day')->pluck('count', 'day');

        return collect(range(6, 0))->map(function (int $daysAgo) use ($quoteCounts, $messageCounts): array {
            $date = now()->subDays($daysAgo);
            $day = $date->toDateString();

            return ['day' => $date->format('D'), 'count' => (int) ($quoteCounts->get($day, 0) + $messageCounts->get($day, 0))];
        })->values()->all();
    }

    #[Computed]
    public function quoteStatusBreakdown(): array
    {
        $counts = QuoteRequest::query()->selectRaw('status, COUNT(*) as count')->groupBy('status')->pluck('count', 'status');

        return collect(['new', 'contacted', 'quoted', 'won', 'lost'])->map(fn (string $status): array => ['status' => $status, 'count' => (int) $counts->get($status, 0)])->all();
    }

    #[Computed]
    public function totalQuoteRequests(): int
    {
        return QuoteRequest::count();
    }

    #[Computed]
    public function maxLeads(): int
    {
        return max(1, collect($this->leadsLast7Days)->max('count'));
    }

    #[Computed]
    public function recentActivity(): Collection
    {
        $quotes = QuoteRequest::query()->latest()->limit(5)->get(['id', 'full_name', 'created_at'])->map(fn (QuoteRequest $quote): array => ['id' => 'quote-'.$quote->id, 'url' => route('admin.leads.quote-requests'), 'tone' => 'copper', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75"/>', 'text' => "New quote request from {$quote->full_name}", 'time' => $quote->created_at->diffForHumans(), 'created_at' => $quote->created_at]);
        $messages = ContactMessage::query()->latest()->limit(5)->get(['id', 'full_name', 'created_at'])->map(fn (ContactMessage $message): array => ['id' => 'message-'.$message->id, 'url' => route('admin.leads.contact-messages'), 'tone' => 'sage', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75l-9.75 6-9.75-6"/>', 'text' => "New message from {$message->full_name}", 'time' => $message->created_at->diffForHumans(), 'created_at' => $message->created_at]);

        return $quotes->merge($messages)->sortByDesc('created_at')->take(8)->values();
    }

    public function badgeBarClass(string $status): string
    {
        return match ($status) {
            'new' => 'bg-copper-500',
            'won' => 'bg-sage-500',
            'lost' => 'bg-danger-500',
            default => 'bg-ink-900/30 dark:bg-linen-100/30',
        };
    }

    public function badgeSoftClass(string $tone): string
    {
        return match ($tone) {
            'sage' => 'bg-sage-500/15 text-sage-600 dark:text-sage-400',
            'danger' => 'bg-danger-500/15 text-danger-600 dark:text-danger-400',
            default => 'bg-copper-500/15 text-copper-600 dark:text-copper-300',
        };
    }

    // Returns BOTH the display string and whether the metric genuinely
    // increased - previously the caller had to guess/hardcode the
    // direction separately, which is exactly how the trendUp bug happened.
    private function trend(int $current, int $previous): array
    {
        $percent = $previous === 0
            ? ($current > 0 ? 100 : 0)
            : (($current - $previous) / $previous) * 100;

        return [
            'label' => sprintf('%+.0f%%', $percent),
            'increased' => $current > $previous,
        ];
    }

    public function render()
    {
        return view('livewire.admin.overview');
    }
}
