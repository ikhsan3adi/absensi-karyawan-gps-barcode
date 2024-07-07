<?php

namespace App\Livewire\Admin\ImportExport;

use Livewire\Component;
use App\Models\Division;
use App\Models\JobTitle;
use App\Models\Education;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Illuminate\Support\Carbon;
use App\Exports\AttendancesExport;
use App\Imports\AttendancesImport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Builder;

use Laravel\Jetstream\InteractsWithBanner;
use App\Models\Attendance as AttendanceModel;

class Attendance extends Component
{
    use InteractsWithBanner, WithFileUploads;

    public bool $previewing = false;
    public ?string $mode = null;
    public $file = null;
    public $year = null;
    public $month = null;
    public $division = null;
    public $job_title = null;
    public $education = null;

    protected $rules = [
        'file' => 'required|mimes:csv,xls,xlsx,ods',
        'year' => 'nullable|date_format:Y',
        'month' => 'nullable|date_format:Y-m',
        'division' => 'nullable|exists:divisions,id',
        'job_title' => 'nullable|exists:job_titles,id',
        'education' => 'nullable|exists:educations,id',
    ];

    public function preview()
    {
        $this->previewing = !$this->previewing;
        $this->mode = $this->previewing ? 'export' : null;
    }

    public function mount()
    {
        $this->year = date('Y');
    }

    public function render()
    {
        $attendances = null;
        if ($this->file) {
            $this->mode = 'import';
            $this->previewing = true;
            $attendanceImport = new AttendancesImport(save: false);
            $attendances = Excel::toCollection($attendanceImport, $this->file)
                ->first()
                ->map(function (\Illuminate\Support\Collection $row) use ($attendanceImport) {
                    return $attendanceImport->model($row->toArray());
                });
        } else if ($this->previewing && $this->mode == 'export') {
            $attendances = AttendanceModel::filter(
                month: $this->month,
                year: $this->year,
                division: $this->division,
                jobTitle: $this->job_title,
                education: $this->education
            )->get();
        } else {
            $this->previewing = false;
            $this->mode = null;
        }
        return view('livewire.admin.import-export.attendance', [
            'attendances' => $attendances
        ]);
    }

    public function import()
    {
        if (Auth::user()->isNotAdmin) {
            abort(403);
        }
        try {
            $this->validate();

            Excel::import(new AttendancesImport, $this->file);

            $this->banner(__('Success'));
            $this->reset();
        } catch (\Throwable $th) {
            $this->dangerBanner($th->getMessage());
        }
    }

    public function export()
    {
        if (Auth::user()->isNotAdmin) {
            abort(403);
        }

        $division = $this->division ? Division::find($this->division)?->name : null;
        $job_title = $this->job_title ? JobTitle::find($this->job_title)?->name : null;
        $education = $this->education ? Education::find($this->education)?->name : null;

        $filename = 'attendances' . ($this->month ? '_' . Carbon::parse($this->month)->format('F-Y') : '') . ($this->year && !$this->month ? '_' . $this->year : '') . ($division ? '_' . Str::slug($division) : '') . ($job_title ? '_' . Str::slug($job_title) : '') . ($education ? '_' . Str::slug($education) : '') . '.xlsx';

        return Excel::download(new AttendancesExport(
            $this->month,
            $this->year,
            $this->division,
            $this->job_title,
            $this->education
        ), $filename);
    }
}
