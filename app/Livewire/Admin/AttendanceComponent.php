<?php

namespace App\Livewire\Admin;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Laravel\Jetstream\InteractsWithBanner;
use Livewire\Component;
use Livewire\WithPagination;

class AttendanceComponent extends Component
{
    use WithPagination, InteractsWithBanner;

    public ?string $month;
    public ?string $week = null;
    public ?string $date = null;
    public ?string $division = null;
    public ?string $jobTitle = null;
    public ?string $search = null;
    public bool $showAttachment = false;
    public $currentAttachment = [];

    public function showAttendanceAttachment($note, $attachment)
    {
        $this->showAttachment = true;
        $this->currentAttachment = [
            'note' => $note,
            'attachment' => $attachment,
        ];
    }

    public function mount()
    {
        $this->date = date('Y-m-d');
    }

    public function updating($key): void
    {
        if ($key === 'search' || $key === 'division' || $key === 'jobTitle') {
            $this->resetPage();
        }
        if ($key === 'month') {
            $this->resetPage();
            $this->week = null;
            $this->date = null;
        }
        if ($key === 'week') {
            $this->resetPage();
            $this->month = null;
            $this->date = null;
        }
        if ($key === 'date') {
            $this->resetPage();
            $this->month = null;
            $this->week = null;
        }
    }

    public function render()
    {
        if ($this->date) {
            $dates = [Carbon::parse($this->date)];
        } else if ($this->week) {
            $start = Carbon::parse($this->week)->startOfWeek();
            $end = Carbon::parse($this->week)->endOfWeek();
            $dates = $start->range($end)->toArray();
        } else if ($this->month) {
            $start = Carbon::parse($this->month)->startOfMonth();
            $end = Carbon::parse($this->month)->endOfMonth();
            $dates = $start->range($end)->toArray();
        }
        $employees = User::where('group', 'user')
            ->when($this->search, fn ($q) => $q->where('name', 'like', '%' . $this->search . '%')->orWhere('nip', 'like', '%' . $this->search . '%'))
            ->when($this->division, fn ($q) => $q->where('division_id', $this->division))
            ->when($this->jobTitle, fn ($q) => $q->where('job_title_id', $this->jobTitle))
            ->paginate(20)->through(function ($user) {
                if ($this->date) {
                    $attendances = new Collection(Cache::remember(
                        "attendance-$user->id-$this->date",
                        now()->addHour(),
                        function () use ($user) {
                            $date = Carbon::parse($this->date);

                            /** @var Collection<Attendance>  */
                            $attendances = Attendance::where('user_id', $user->id)
                                ->where('date', $date->toDateString())
                                ->get();

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
                } else if ($this->week) {
                    $attendances = new Collection(Cache::remember(
                        "attendance-$user->id-$this->week",
                        now()->addHour(),
                        function () use ($user) {
                            $start = Carbon::parse($this->week)->startOfWeek();
                            $end = Carbon::parse($this->week)->endOfWeek();

                            /** @var Collection<Attendance>  */
                            $attendances = Attendance::where('user_id', $user->id)
                                ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
                                ->get(['id', 'status', 'date']);

                            return $attendances->map(fn ($v) => $v->getAttributes())->toArray();
                        }
                    ) ?? []);
                } else if ($this->month) {
                    $my = Carbon::parse($this->month);
                    $attendances = new Collection(Cache::remember(
                        "attendance-$user->id-$my->month-$my->year",
                        now()->addHour(),
                        function () use ($user, $my) {
                            /** @var Collection<Attendance>  */
                            $attendances = Attendance::where('user_id', $user->id)
                                ->whereMonth('date', $my->month)
                                ->whereYear('date', $my->year)
                                ->get(['id', 'status', 'date']);

                            return $attendances->map(fn ($v) => $v->getAttributes())->toArray();
                            // return [
                            //     'present' => $attendances->where('status', 'present')->count(),
                            //     'late' => $attendances->where('status', 'late')->count(),
                            //     'sick' => $attendances->where('status', 'sick')->count(),
                            //     'excused' => $attendances->where('status', 'excused')->count(),
                            //     'absent' => $attendances->where('status', 'absent')->count(),
                            // ];
                        }
                    ) ?? []);
                } else {
                    /** @var Collection */
                    $attendances = Attendance::paginate(20);
                }
                $user->attendances = $attendances;
                return $user;
            });
        return view('livewire.admin.attendance', ['employees' => $employees, 'dates' => $dates]);
    }
}
