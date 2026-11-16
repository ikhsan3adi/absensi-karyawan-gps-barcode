<?php

namespace App\Livewire\Traits;

use App\Models\Attendance;

trait AttendanceDetailTrait
{
    public bool $showDetail = false;

    public $currentAttendance = [];

    public function show($attendanceId)
    {
        $user = auth()->user();

        abort_unless($user, 403);

        /** @var Attendance|null */
        $attendance = Attendance::query()
            ->when(! $user->isAdmin, fn ($query) => $query->where('user_id', $user->id))
            ->find($attendanceId);

        if ($attendance) {
            $this->showDetail = true;
            $this->currentAttendance = $attendance->getAttributes();
            $this->currentAttendance['name'] = $attendance->user->name;
            $this->currentAttendance['nip'] = $attendance->user->nip;
            $this->currentAttendance['address'] = $attendance->user->address;
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
}
