<?php

use App\Livewire\AttendanceHistoryComponent;
use App\Models\Attendance;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->employee = User::factory()->create(['group' => 'user']);
    $this->otherEmployee = User::factory()->create(['group' => 'user']);
    $this->admin = User::factory()->admin()->create();

    $this->otherAttendance = Attendance::create([
        'user_id' => $this->otherEmployee->id,
        'date' => '2026-11-10',
        'time_in' => '08:00:00',
        'status' => 'present',
        'latitude' => 1.2345,
        'longitude' => 103.1234,
        'note' => 'PRIVATE-NOTE',
    ]);
});

test('employee can view their own attendance detail', function () {
    $ownAttendance = Attendance::create([
        'user_id' => $this->employee->id,
        'date' => '2026-11-11',
        'time_in' => '08:00:00',
        'status' => 'present',
        'note' => 'MY-NOTE',
    ]);

    Livewire::actingAs($this->employee)
        ->test(AttendanceHistoryComponent::class)
        ->call('show', $ownAttendance->id)
        ->assertSet('showDetail', true)
        ->assertSet('currentAttendance.name', $this->employee->name)
        ->assertSet('currentAttendance.note', 'MY-NOTE');
});

test('employee cannot view another users attendance detail', function () {
    Livewire::actingAs($this->employee)
        ->test(AttendanceHistoryComponent::class)
        ->call('show', $this->otherAttendance->id)
        ->assertSet('showDetail', false)
        ->assertSet('currentAttendance', []);
});

test('admin can view any attendance detail', function () {
    Livewire::actingAs($this->admin)
        ->test(AttendanceHistoryComponent::class)
        ->call('show', $this->otherAttendance->id)
        ->assertSet('showDetail', true)
        ->assertSet('currentAttendance.name', $this->otherEmployee->name)
        ->assertSet('currentAttendance.note', 'PRIVATE-NOTE');
});
