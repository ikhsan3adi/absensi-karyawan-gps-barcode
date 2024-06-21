<?php

namespace App\Livewire;

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

    public function mount()
    {
        $this->month = date('Y-m');
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
        if (!$this->month) {
            $this->month = date('Y-m');
        }
        $employees = User::where('group', 'user')
            ->when($this->search, fn ($q) => $q->where('name', 'like', '%' . $this->search . '%')->orWhere('nip', 'like', '%' . $this->search . '%'))
            ->when($this->division, fn ($q) => $q->where('division_id', $this->division))
            ->when($this->jobTitle, fn ($q) => $q->where('job_title_id', $this->jobTitle))
            ->paginate(20)->through(function ($user) {
                if ($this->date) {
                    $attendances = Cache::remember(
                        "attendance-$user->id-$this->date",
                        now()->addHour(),
                        function () use ($user) {
                            $date = Carbon::parse($this->date);

                            /** @var Collection<Attendance>  */
                            $attendances = Attendance::where('user_id', $user->id)
                                ->where('date', $date->toDateString())
                                ->get();

                            return $attendances->map(fn ($v) => $v->getAttributes())->toArray();
                        }
                    ) ?? [];
                } else if ($this->week) {
                    $attendances = Cache::remember(
                        "attendance-$user->id-$this->week",
                        now()->addHour(),
                        function () use ($user) {
                            $start = Carbon::parse($this->week)->startOfWeek();
                            $end = Carbon::parse($this->week)->endOfWeek();

                            /** @var Collection<Attendance>  */
                            $attendances = Attendance::where('user_id', $user->id)
                                ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
                                ->get();

                            return $attendances->map(fn ($v) => $v->getAttributes())->toArray();
                        }
                    ) ?? [];
                } else {
                    $my = Carbon::parse($this->month);
                    $attendances = Cache::remember(
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
                    ) ?? [];
                }
                $user->attendances = $attendances;
                return $user;
            });
        dd($employees);
        return view('livewire.attendance', ['employees' => $employees]);
    }
}
