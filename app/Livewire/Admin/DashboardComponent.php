<?php

namespace App\Livewire\Admin;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class DashboardComponent extends Component
{
    public bool $showDetail = false;
    public $currentAttendance = [];

    public function show($attendanceId)
    {
        /** @var Attendance */
        $attendance = Attendance::find($attendanceId);
        if ($attendance) {
            $this->showDetail = true;
            $this->currentAttendance = $attendance->getAttributes();
            $this->currentAttendance['lat'] = $attendance->latitude;
            $this->currentAttendance['lng'] = $attendance->longitude;
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

    public function render()
    {
        /** @var Collection<Attendance>  */
        $attendances = Attendance::where('date', date('Y-m-d'))->get();

        /** @var Collection<User>  */
        $employees = User::where('group', 'user')
            ->paginate(20)
            ->through(function (User $user) use ($attendances) {
                return $user->setAttribute(
                    'attendance',
                    $attendances
                        ->where(fn (Attendance $attendance) => $attendance->user_id === $user->id)
                        ->first(),
                );
            });

        $employeesCount = User::where('group', 'user')->count();
        $presentCount = $attendances->where(fn ($attendance) => $attendance->status === 'present')->count();
        $lateCount = $attendances->where(fn ($attendance) => $attendance->status === 'late')->count();
        $excusedCount = $attendances->where(fn ($attendance) => $attendance->status === 'excused')->count();
        $sickCount = $attendances->where(fn ($attendance) => $attendance->status === 'sick')->count();
        $absentCount = $employeesCount - ($presentCount + $lateCount + $excusedCount + $sickCount);

        return view('livewire.admin.dashboard', [
            'employees' => $employees,
            'employeesCount' => $employeesCount,
            'presentCount' => $presentCount,
            'lateCount' => $lateCount,
            'excusedCount' => $excusedCount,
            'sickCount' => $sickCount,
            'absentCount' => $absentCount,
        ]);
    }
}
