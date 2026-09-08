<?php

namespace App\Livewire\Concerns;

use NumberFormatter;

trait HasNumberInWords
{
    public function numberInWords(int $number): string
    {
        $formatter = new NumberFormatter(
            'en',
            NumberFormatter::SPELLOUT
        );

        return $formatter->format($number);
    }
}