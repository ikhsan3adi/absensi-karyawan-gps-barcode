<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AttendancesExport;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Imports\AttendancesImport;
use Maatwebsite\Excel\Facades\Excel;

class ImportExportController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.import-export.index');
    }

    public function importUsers(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimes:csv,xls,xlsx,ods'
            ]);

            $file = $request->file('file');

            Excel::import(new UsersImport, $file);

            return redirect()->back()->with('flash.banner', __('Success'));
        } catch (\Throwable $th) {
            return redirect()->back()
                ->with('flash.banner', $th->getMessage())
                ->with('flash.bannerStyle', 'danger');
        }
    }

    public function exportUsers(Request $request)
    {
        return Excel::download(new UsersExport, 'users.xlsx');
    }

    public function importAttendances(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimes:csv,xls,xlsx,ods'
            ]);

            $file = $request->file('file');

            Excel::import(new AttendancesImport, $file);

            return redirect()->back()->with('flash.banner', __('Success'));
        } catch (\Throwable $th) {
            return redirect()->back()
                ->with('flash.banner', $th->getMessage())
                ->with('flash.bannerStyle', 'danger');
        }
    }

    public function exportAttendances(Request $request)
    {
        $request->validate([
            'year' => 'nullable|date_format:Y',
            'month' => 'nullable|date_format:Y-m',
            'division' => 'nullable|exists:divisions,id',
            'job_title' => 'nullable|exists:job_titles,id',
            'education' => 'nullable|exists:educations,id',
        ]);

        $filename = 'attendances' . ($request->year && !$request->month ? '-' . $request->year : '') . ($request->month ? '-' . $request->month : '') . ($request->division ? '-' . $request->division : '') . ($request->job_title ? '-' . $request->job_title : '') . ($request->education ? '-' . $request->education : '') . '.xlsx';

        return Excel::download(new AttendancesExport(
            $request->month,
            $request->year,
            $request->division,
            $request->job_title,
            $request->education
        ), $filename);
    }
}
