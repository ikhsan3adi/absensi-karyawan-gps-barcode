<?php

use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Support\Carbon;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create(['group' => 'user']);
    $this->admin = User::factory()->create(['group' => 'admin']);
});

test('employee can submit a leave request', function () {
    $response = actingAs($this->user)->post(route('store-leave-request'), [
        'status' => 'excused',
        'note' => 'Ada acara keluarga',
        'from' => '2026-07-01',
        'to' => '2026-07-02',
    ]);

    $response->assertRedirect(route('home'));
    $response->assertSessionHas('flash.banner');

    $this->assertDatabaseHas('leave_requests', [
        'user_id' => $this->user->id,
        'type' => 'excused',
        'from_date' => '2026-07-01',
        'to_date' => '2026-07-02',
        'note' => 'Ada acara keluarga',
        'status' => 'pending',
    ]);
});

test('leave request is created with pending status', function () {
    $leaveRequest = LeaveRequest::create([
        'user_id' => $this->user->id,
        'type' => 'sick',
        'from_date' => '2026-07-03',
        'to_date' => '2026-07-03',
        'note' => 'Demam',
        'status' => 'pending',
    ]);

    expect($leaveRequest->status)->toBe('pending');
});

test('admin can approve leave request', function () {
    $leaveRequest = LeaveRequest::create([
        'user_id' => $this->user->id,
        'type' => 'excused',
        'from_date' => '2026-07-05',
        'to_date' => '2026-07-05',
        'note' => 'Izin',
        'status' => 'pending',
    ]);

    Carbon::setTestNow(Carbon::parse('2026-06-20 10:00:00'));

    actingAs($this->admin);

    $leaveRequest->update([
        'status' => 'approved',
        'reviewed_by' => $this->admin->id,
        'reviewed_at' => now(),
    ]);

    $this->assertDatabaseHas('leave_requests', [
        'id' => $leaveRequest->id,
        'status' => 'approved',
        'reviewed_by' => $this->admin->id,
    ]);
});

test('approved leave request creates attendance records', function () {
    $leaveRequest = LeaveRequest::create([
        'user_id' => $this->user->id,
        'type' => 'excused',
        'from_date' => '2026-07-10',
        'to_date' => '2026-07-12',
        'note' => 'Cuti',
        'status' => 'approved',
    ]);

    Carbon::parse($leaveRequest->from_date)
        ->range(Carbon::parse($leaveRequest->to_date))
        ->forEach(function (Carbon $date) use ($leaveRequest) {
            Attendance::updateOrCreate(
                [
                    'user_id' => $leaveRequest->user_id,
                    'date' => $date->toDateString(),
                ],
                [
                    'status' => $leaveRequest->type,
                    'note' => $leaveRequest->note,
                ]
            );
        });

    $this->assertDatabaseHas('attendances', [
        'user_id' => $this->user->id,
        'date' => '2026-07-10',
        'status' => 'excused',
        'note' => 'Cuti',
    ]);

    $this->assertDatabaseHas('attendances', [
        'user_id' => $this->user->id,
        'date' => '2026-07-12',
        'status' => 'excused',
    ]);

    $this->assertDatabaseMissing('attendances', [
        'user_id' => $this->user->id,
        'date' => '2026-07-13',
    ]);
});

test('admin can reject leave request with reason', function () {
    $leaveRequest = LeaveRequest::create([
        'user_id' => $this->user->id,
        'type' => 'sick',
        'from_date' => '2026-07-15',
        'to_date' => '2026-07-15',
        'note' => 'Sakit',
        'status' => 'pending',
    ]);

    actingAs($this->admin);

    $leaveRequest->update([
        'status' => 'rejected',
        'reviewed_by' => $this->admin->id,
        'reviewed_at' => now(),
        'rejection_reason' => 'Surat dokter tidak lengkap',
    ]);

    $this->assertDatabaseHas('leave_requests', [
        'id' => $leaveRequest->id,
        'status' => 'rejected',
        'rejection_reason' => 'Surat dokter tidak lengkap',
    ]);
});

test('rejected leave request does not create attendance records', function () {
    $leaveRequest = LeaveRequest::create([
        'user_id' => $this->user->id,
        'type' => 'excused',
        'from_date' => '2026-07-20',
        'to_date' => '2026-07-20',
        'note' => 'Izin',
        'status' => 'rejected',
    ]);

    expect(Attendance::where('user_id', $this->user->id)
        ->where('date', '2026-07-20')
        ->exists()
    )->toBeFalse();
});

test('leave request belongs to user', function () {
    $leaveRequest = LeaveRequest::factory()->create([
        'user_id' => $this->user->id,
    ]);

    expect($leaveRequest->user->id)->toBe($this->user->id);
    expect($leaveRequest->user->name)->toBe($this->user->name);
});
