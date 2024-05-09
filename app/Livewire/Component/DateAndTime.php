<?php

namespace App\Livewire\Component;

use Livewire\Component;

class DateAndTime extends Component
{
    public function render()
    {
        return view('livewire.component.date-and-time', [
            'currentDateTime' => now()->format('M d h:i:s A'),
        ]);
    }
}
