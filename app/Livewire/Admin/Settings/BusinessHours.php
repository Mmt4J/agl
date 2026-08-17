<?php

namespace App\Livewire\Admin\Settings;

use App\Models\BusinessHour;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::admin')]
#[Title('Business Hours')]
class BusinessHours extends Component
{
    // Keyed by day_of_week (0=Sunday..6=Saturday, Carbon's convention -
    // matches the unique constraint on that column). Display order in
    // the view is Monday-first; storage order doesn't have to match.
    public array $hours = [];

    public bool $justSaved = false;

    public function mount(): void
    {
        $rows = BusinessHour::orderBy('day_of_week')->get()->keyBy('day_of_week');

        foreach (range(0, 6) as $day) {
            $row = $rows->get($day);

            $this->hours[$day] = [
                'opens_at' => $row?->opens_at ? substr($row->opens_at, 0, 5) : '',
                'closes_at' => $row?->closes_at ? substr($row->closes_at, 0, 5) : '',
                'is_closed' => (bool) $row?->is_closed,
            ];
        }
    }

    public function save(): void
    {
        $this->justSaved = false;

        foreach ($this->hours as $day => $data) {
            BusinessHour::updateOrCreate(
                ['day_of_week' => $day],
                [
                    // Closed days store null times regardless of whatever
                    // was left in the inputs - avoids stale hours quietly
                    // reappearing if "closed" gets unchecked later.
                    'opens_at' => $data['is_closed'] ? null : ($data['opens_at'] ?: null),
                    'closes_at' => $data['is_closed'] ? null : ($data['closes_at'] ?: null),
                    'is_closed' => $data['is_closed'],
                ]
            );
        }

        $this->justSaved = true;
    }

    public function render()
    {
        return view('livewire.admin.settings.business-hours', [
            // Monday(1)..Saturday(6), then Sunday(0) last - natural week
            // reading order, independent of the DB's Sunday-first storage.
            'dayOrder' => [1, 2, 3, 4, 5, 6, 0],
            'dayLabels' => [
                0 => 'Sunday', 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday',
                4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday',
            ],
        ]);
    }
}
