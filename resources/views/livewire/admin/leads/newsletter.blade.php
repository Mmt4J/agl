<div
    x-data="newsletterManager(@js($this->subscribers))"
    class="space-y-4"
>

    <div class="flex flex-wrap items-center justify-between gap-3">

        <div class="flex flex-wrap gap-2">

            <template x-for="s in ['All','subscribed','unsubscribed']" :key="s">

                <button
                    @click="newsletterFilter=s"
                    class="px-3 py-1.5 rounded-full text-xs font-medium border capitalize transition"
                    :class="
                        newsletterFilter===s
                        ?
                        'bg-ink-900 dark:bg-copper-500 text-white dark:text-ink-950 border-ink-900 dark:border-copper-500'
                        :
                        'border-ink-900/15 dark:border-white/15 hover:bg-ink-900/5'
                    "
                    x-text="s"
                >
                </button>

            </template>

        </div>


        <button
            @click="exportSubscribersCsv()"
            class="inline-flex items-center gap-2 rounded-md border px-3 py-1.5 text-xs hover:bg-gray-100 dark:hover:bg-white/5"
        >

            <svg class="w-4 h-4"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"
                />

            </svg>

            Export CSV

        </button>

    </div>



    {{-- Desktop Table --}}

    <div class="hidden md:block overflow-hidden rounded-md border border-ink-900/10 dark:border-linen-100/10 bg-white dark:bg-ink-900/40">

        <table class="w-full text-sm">

            <thead class="bg-gray-50 dark:bg-white/5 text-left text-xs uppercase">

            <tr>

                <th class="px-5 py-3">
                    Email
                </th>

                <th class="px-5 py-3">
                    Status
                </th>

                <th class="px-5 py-3">
                    Subscribed
                </th>

            </tr>

            </thead>


            <tbody class="divide-y divide-ink-900/10 dark:divide-linen-100/10">

            <template x-for="sub in filteredSubscribers" :key="sub.id">

                <tr class="hover:bg-black/5 dark:hover:bg-white/5">

                    <td
                        class="px-5 py-3 font-mono text-xs"
                        x-text="sub.email">
                    </td>


                    <td class="px-5 py-3">

                        <span
                            class="rounded-full px-2.5 py-1 text-xs capitalize"
                            :class="badgeSoftClass(sub.status)"
                            x-text="sub.status">
                        </span>

                    </td>


                    <td
                        class="px-5 py-3 text-xs opacity-60"
                        x-text="sub.subscribedAt">
                    </td>


                </tr>

            </template>


            </tbody>


        </table>

    </div>



    {{-- Mobile Cards --}}

    <div class="md:hidden space-y-3">


        <template x-for="sub in filteredSubscribers" :key="'mobile-'+sub.id">


            <div
                class="rounded-md border bg-white dark:bg-ink-900/40 p-4 flex justify-between gap-3"
            >


                <div class="min-w-0">

                    <p
                        class="truncate font-mono text-xs"
                        x-text="sub.email">
                    </p>


                    <p
                        class="mt-1 text-[10px] opacity-50"
                        x-text="sub.subscribedAt">
                    </p>

                </div>


                <span
                    class="h-fit rounded-full px-2 py-1 text-[10px] capitalize"
                    :class="badgeSoftClass(sub.status)"
                    x-text="sub.status">
                </span>


            </div>


        </template>


    </div>



</div>



<script>
document.addEventListener('alpine:init', () => {

    Alpine.data('newsletterManager', (subscribers = []) => ({

        subscribers,

        newsletterFilter: 'All',


        get filteredSubscribers()
        {
            if (this.newsletterFilter === 'All') {
                return this.subscribers;
            }

            return this.subscribers.filter(
                subscriber => subscriber.status === this.newsletterFilter
            );
        },


        badgeSoftClass(status)
        {
            return status === 'subscribed'
                ? 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300'
                : 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300';
        },


        exportSubscribersCsv()
        {
            const rows = [
                [
                    'Email',
                    'Status',
                    'Subscribed'
                ]
            ];


            this.filteredSubscribers.forEach(subscriber => {

                rows.push([
                    subscriber.email,
                    subscriber.status,
                    subscriber.subscribedAt
                ]);

            });


            const csv = rows
                .map(row =>
                    row.map(
                        value => `"${value ?? ''}"`
                    ).join(',')
                )
                .join('\n');


            const blob = new Blob(
                [csv],
                {
                    type: 'text/csv;charset=utf-8;'
                }
            );


            const url = URL.createObjectURL(blob);


            const link = document.createElement('a');

            link.href = url;

            link.download = 'newsletter-subscribers.csv';

            document.body.appendChild(link);

            link.click();

            document.body.removeChild(link);


            URL.revokeObjectURL(url);
        }

    }));

});
</script>