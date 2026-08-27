<div>
    <h1 class="font-display text-2xl font-semibold text-ink-950 dark:text-linen-50">Overview</h1>

    <div class="space-y-6">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            @foreach ($this->kpis as $kpi)
                <div class="rounded-md border border-ink-900/10 dark:border-linen-100/10 bg-white dark:bg-ink-900/40 p-4 sm:p-5">
                    <div class="flex items-center justify-between mb-2">
                        <span class="w-8 h-8 rounded-md bg-ink-900/5 dark:bg-linen-100/5 text-copper-600 dark:text-copper-300 grid place-items-center">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">{!! $kpi['icon'] !!}</svg>
                        </span>
                        <span class="font-mono text-[10px] px-1.5 py-0.5 rounded-full {{ $kpi['trendUp'] ? 'bg-sage-500/15 text-sage-600 dark:text-sage-500' : 'bg-danger-500/15 text-danger-500' }}">{{ $kpi['trend'] }}</span>
                    </div>
                    <p class="font-display font-semibold text-2xl sm:text-3xl">{{ $kpi['value'] }}</p>
                    <p class="text-xs text-ink-900/55 dark:text-linen-100/55 mt-1">{{ $kpi['label'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="grid lg:grid-cols-3 gap-4 sm:gap-6">
            <div class="lg:col-span-2 rounded-md border border-ink-900/10 dark:border-linen-100/10 bg-white dark:bg-ink-900/40 p-5 sm:p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="font-display font-semibold">Leads, last 7 days</h2>
                    <span class="font-mono text-[10px] text-ink-900/40 dark:text-linen-100/40">Quotes + messages</span>
                </div>
                <div class="flex items-end gap-3 sm:gap-4 h-40">
                    @foreach ($this->leadsLast7Days as $day)
                        <div class="flex-1 flex flex-col items-center gap-2">
                            <div class="w-full flex items-end justify-center h-32 gap-1">
                                <div class="w-3 sm:w-4 rounded-t bg-copper-400" style="height: {{ ($day['count'] / $this->maxLeads) * 100 }}%" title="{{ $day['count'] }} leads"></div>
                            </div>
                            <span class="font-mono text-[10px] text-ink-900/50 dark:text-linen-100/50">{{ $day['day'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-md border border-ink-900/10 dark:border-linen-100/10 bg-white dark:bg-ink-900/40 p-5 sm:p-6">
                <h2 class="font-display font-semibold mb-6">Quote requests by status</h2>
                <div class="space-y-4">
                    @foreach ($this->quoteStatusBreakdown as $status)
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <span class="capitalize font-medium">{{ $status['status'] }}</span>
                                <span class="font-mono text-ink-900/50 dark:text-linen-100/50">{{ $status['count'] }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-ink-900/8 dark:bg-linen-100/10 overflow-hidden">
                                <div class="h-full rounded-full {{ $this->badgeBarClass($status['status']) }}" style="width: {{ $this->totalQuoteRequests ? ($status['count'] / $this->totalQuoteRequests) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="rounded-md border border-ink-900/10 dark:border-linen-100/10 bg-white dark:bg-ink-900/40">
            <div class="flex items-center justify-between px-5 sm:px-6 py-4 border-b border-ink-900/10 dark:border-linen-100/10">
                <h2 class="font-display font-semibold">Recent activity</h2>
            </div>
            <ul class="divide-y divide-ink-900/10 dark:divide-linen-100/10">
                @forelse ($this->recentActivity as $activity)
                    <li wire:key="{{ $activity['id'] }}" class="flex items-start gap-3 px-5 sm:px-6 py-4 hover:bg-ink-900/2 dark:hover:bg-linen-100/3">
                        <span class="w-8 h-8 rounded-full grid place-items-center shrink-0 {{ $this->badgeSoftClass($activity['tone']) }}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">{!! $activity['icon'] !!}</svg>
                        </span>
                        <a href="{{ $activity['url'] }}" class="min-w-0 flex-1">
                            <p class="text-sm">{{ $activity['text'] }}</p>
                            <p class="font-mono text-[10px] text-ink-900/40 dark:text-linen-100/40 mt-1">{{ $activity['time'] }}</p>
                        </a>
                    </li>
                @empty
                    <li class="px-5 sm:px-6 py-8 text-sm text-ink-900/50 dark:text-linen-100/50">No recent activity.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
