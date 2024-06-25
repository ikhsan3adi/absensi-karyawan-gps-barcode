<?php

namespace App\Livewire;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class AttendanceHistoryComponent extends Component
{
    public ?string $month;
    public bool $showDetail = false;
    public $currentAttendance = [];

    public function show($attendanceId)
    {
        /** @var Attendance */
        $attendance = Attendance::find($attendanceId);
        if ($attendance) {
            $this->showDetail = true;
            $this->currentAttendance = $attendance->getAttributes();
            $this->currentAttendance['coordinates'] = $attendance->lat_lng;
            $this->currentAttendance['name'] = $attendance->user->name;
            $this->currentAttendance['nip'] = $attendance->user->nip;
            if ($attendance->attachment) {
                $this->currentAttendance['attachment'] = $attendance->attachment_url;
            }
            if ($attendance->barcode_id) {
                $this->currentAttendance['barcode'] = $attendance->barcode;
            }
            if ($attendance->shift_id) {
                $this->currentAttendance['shift'] = $attendance->shift;
            }
        }
    }
    public function mount()
    {
        $this->month = date('Y-m');
    }

    public function render()
    {
        $user = auth()->user();
        $date = Carbon::parse($this->month);

        $start = Carbon::parse($this->month)->startOfMonth();
        $end = Carbon::parse($this->month)->endOfMonth();
        $dates = $start->range($end)->toArray();

        $attendances = new Collection(Cache::remember(
            "attendance-$user->id-$date->month-$date->year",
            now()->addMinutes(5),
            function () use ($user, $date) {
                /** @var Collection<Attendance>  */
                $attendances = Attendance::where('user_id', $user->id)
                    ->whereMonth('date', $date->month)
                    ->whereYear('date', $date->year)
                    ->get(['id', 'status', 'date', 'coordinates', 'attachment', 'note']);

                return $attendances->map(
                    function (Attendance $v) {
                        $v->setAttribute('coordinates', $v->lat_lng);
                        if ($v->attachment) {
                            $v->setAttribute('attachment', $v->attachment_url);
                        }
                        return $v->getAttributes();
                    }
                )->toArray();
            }
        ) ?? []);
        $attendanceToday = $attendances->firstWhere(fn ($v, $_) => $v['date'] === Carbon::now()->format('Y-m-d'));
        return view('livewire.attendance-history', [
            'attendances' => $attendances,
            'attendanceToday' => $attendanceToday,
            'dates' => $dates,
            'start' => $start,
            'end' => $end,
        ]);
    }
}
