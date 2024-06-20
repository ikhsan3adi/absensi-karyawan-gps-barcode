<?php

namespace App\Livewire;

use App\Models\Attendance;
use Laravel\Jetstream\InteractsWithBanner;
use Livewire\Component;
use Livewire\WithPagination;

class AttendanceComponent extends Component
{
    use WithPagination, InteractsWithBanner;

    public function render()
    {
        $attendances = Attendance::paginate(20);
        return view('livewire.attendance', ['attendances' => $attendances]);
    }
}
