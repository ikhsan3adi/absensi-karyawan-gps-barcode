<?php

namespace App\Exports;

use App\Models\Attendance;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromView;

class AttendancesExport implements FromView
{
    public function __construct(
        private $month = null,
        private $year = null,
        private $division = null,
        private $jobTitle = null,
        private $education = null
    ) {
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        $attendances = Attendance::when($this->month, function (Builder $query) {
            $date = Carbon::parse($this->month);
            $query->whereMonth('date', $date->month)->whereYear('date', $date->year);
        })->when($this->year && !$this->month, function (Builder $query) {
            $date = Carbon::parse($this->year);
            $query->whereYear('date', $date->year);
        })->when($this->division, function (Builder $query) {
            $query->where('user.division_id', $this->division);
        })->when($this->jobTitle, function (Builder $query) {
            $query->where('user.job_title_id', $this->jobTitle);
        })->when($this->education, function (Builder $query) {
            $query->where('user.education_id', $this->education);
        })->get();

        return view('admin.import-export.export-attendances', ['attendances' => $attendances]);
    }
}
