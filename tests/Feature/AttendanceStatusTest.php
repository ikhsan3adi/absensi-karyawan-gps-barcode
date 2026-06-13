<?php

use App\Models\Attendance;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Support\Carbon;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create(['group' => 'user']);
    $this->shift = Shift::factory()->create([
        'start_time' => '08:00:00',
        'end_time' => '17:00:00',
    ]);
});

test('attendance status is present when clocked in on time', function () {
    $now = Carbon::parse('2026-06-13 07:55:00');
    Carbon::setTestNow($now);

    $attendance = Attendance::create([
        'user_id' => $this->user->id,
        'shift_id' => $this->shift->id,
        'date' => $now->format('Y-m-d'),
        'time_in' => $now->format('H:i:s'),
        'status' => $now->copy()->setTimeFromTimeString($this->shift->start_time)->lt($now) ? 'late' : 'present',
    ]);

    expect($attendance->status)->toBe('present');
});

test('attendance status becomes incomplete when clocked out before shift end time', function () {
    Carbon::setTestNow(Carbon::parse('2026-06-13 08:00:00'));

    $attendance = Attendance::create([
        'user_id' => $this->user->id,
        'shift_id' => $this->shift->id,
        'date' => '2026-06-13',
        'time_in' => '08:00:00',
        'status' => 'present',
    ]);

    Carbon::setTestNow(Carbon::parse('2026-06-13 14:00:00'));
    $timeOut = Carbon::now()->format('H:i:s');
    $endTime = Carbon::now()->setTimeFromTimeString($this->shift->end_time);

    $newStatus = $attendance->status;
    if ($this->shift->end_time && Carbon::now()->lt($endTime)) {
        $newStatus = 'incomplete';
    }

    $attendance->update([
        'time_out' => $timeOut,
        'status' => $newStatus,
    ]);

    $attendance->refresh();

    expect($attendance->time_out->format('H:i:s'))->toBe('14:00:00');
    expect($attendance->status)->toBe('incomplete');
});

test('attendance status stays present when clocked out at or after shift end time', function () {
    Carbon::setTestNow(Carbon::parse('2026-06-13 08:00:00'));

    $attendance = Attendance::create([
        'user_id' => $this->user->id,
        'shift_id' => $this->shift->id,
        'date' => '2026-06-13',
        'time_in' => '08:00:00',
        'status' => 'present',
    ]);

    Carbon::setTestNow(Carbon::parse('2026-06-13 17:00:00'));
    $timeOut = Carbon::now()->format('H:i:s');
    $endTime = Carbon::now()->setTimeFromTimeString($this->shift->end_time);

    $newStatus = $attendance->status;
    if ($this->shift->end_time && Carbon::now()->lt($endTime)) {
        $newStatus = 'incomplete';
    }

    $attendance->update([
        'time_out' => $timeOut,
        'status' => $newStatus,
    ]);

    $attendance->refresh();

    expect($attendance->status)->toBe('present');
});

test('attendance status becomes incomplete when clocked out after shift end time but still early due to precision', function () {
    Carbon::setTestNow(Carbon::parse('2026-06-13 08:00:00'));

    $attendance = Attendance::create([
        'user_id' => $this->user->id,
        'shift_id' => $this->shift->id,
        'date' => '2026-06-13',
        'time_in' => '08:30:00',
        'status' => 'late',
    ]);

    Carbon::setTestNow(Carbon::parse('2026-06-13 16:45:00'));
    $timeOut = Carbon::now()->format('H:i:s');
    $endTime = Carbon::now()->setTimeFromTimeString($this->shift->end_time);

    $newStatus = $attendance->status;
    if ($this->shift->end_time && Carbon::now()->lt($endTime)) {
        $newStatus = 'incomplete';
    }

    $attendance->update([
        'time_out' => $timeOut,
        'status' => $newStatus,
    ]);

    $attendance->refresh();

    expect($attendance->status)->toBe('incomplete');
});

test('attendance without shift end time stays present even if clocked out early', function () {
    $shift = Shift::factory()->create([
        'start_time' => '08:00:00',
        'end_time' => null,
    ]);

    Carbon::setTestNow(Carbon::parse('2026-06-13 08:00:00'));

    $attendance = Attendance::create([
        'user_id' => $this->user->id,
        'shift_id' => $shift->id,
        'date' => '2026-06-13',
        'time_in' => '08:00:00',
        'status' => 'present',
    ]);

    Carbon::setTestNow(Carbon::parse('2026-06-13 12:00:00'));
    $timeOut = Carbon::now()->format('H:i:s');
    $newStatus = $attendance->status;
    if ($shift->end_time && Carbon::now()->lt(Carbon::now()->setTimeFromTimeString($shift->end_time))) {
        $newStatus = 'incomplete';
    }

    $attendance->update([
        'time_out' => Carbon::now()->format('H:i:s'),
        'status' => $newStatus,
    ]);

    $attendance->refresh();

    expect($attendance->status)->toBe('present');
});
