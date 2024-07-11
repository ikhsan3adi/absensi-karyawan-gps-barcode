<?php

namespace App\Http\Controllers\Admin;

use App\Models\Attendance;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.attendances.index');
    }

    public function report(Request $request)
    {
        $request->validate([
            'date' => 'nullable|date_format:Y-m-d',
            'month' => 'nullable|date_format:Y-m',
            'week' => 'nullable',
            'division' => 'nullable|exists:divisions,id',
            'job_title' => 'nullable|exists:job_titles,id',
        ]);

        if (!$request->date && !$request->month && !$request->week) {
            return redirect()->back();
        }

        $carbon = new Carbon;

        if ($request->date) {
            $dates = [$carbon->parse($request->date)->settings(['formatFunction' => 'translatedFormat'])];
        } else if ($request->week) {
            $start = $carbon->parse($request->week)->settings(['formatFunction' => 'translatedFormat'])->startOfWeek();
            $end = $carbon->parse($request->week)->settings(['formatFunction' => 'translatedFormat'])->endOfWeek();
            $dates = $start->range($end)->toArray();
        } else if ($request->month) {
            $start = $carbon->parse($request->month)->settings(['formatFunction' => 'translatedFormat'])->startOfMonth();
            $end = $carbon->parse($request->month)->settings(['formatFunction' => 'translatedFormat'])->endOfMonth();
            $dates = $start->range($end)->toArray();
        }
        $employees = User::where('group', 'user')
            ->when($request->division, fn (Builder $q) => $q->where('division_id', $request->division))
            ->when($request->jobTitle, fn (Builder $q) => $q->where('job_title_id', $request->jobTitle))
            ->get()
            ->map(function ($user) use ($request) {
                if ($request->date) {
                    $attendances = new Collection(Cache::remember(
                        "attendance-$user->id-$request->date",
                        now()->addDay(),
                        function () use ($user, $request) {
                            $date = Carbon::parse($request->date);

                            /** @var Collection<Attendance>  */
                            $attendances = Attendance::where('user_id', $user->id)
                                ->where('date', $date->toDateString())
                                ->get();

                            return $attendances->map(
                                function (Attendance $v) {
                                    $v->setAttribute('coordinates', $v->lat_lng);
                                    $v->setAttribute('lat', $v->latitude);
                                    $v->setAttribute('lng', $v->longitude);
                                    if ($v->attachment) {
                                        $v->setAttribute('attachment', $v->attachment_url);
                                    }
                                    if ($v->shift) {
                                        $v->setAttribute('shift', $v->shift->name);
                                    }
                                    return $v->getAttributes();
                                }
                            )->toArray();
                        }
                    ) ?? []);
                } else if ($request->week) {
                    $attendances = new Collection(Cache::remember(
                        "attendance-$user->id-$request->week",
                        now()->addDay(),
                        function () use ($user, $request) {
                            $start = Carbon::parse($request->week)->startOfWeek();
                            $end = Carbon::parse($request->week)->endOfWeek();

                            /** @var Collection<Attendance>  */
                            $attendances = Attendance::where('user_id', $user->id)
                                ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
                                ->get(['id', 'status', 'date']);

                            return $attendances->map(fn ($v) => $v->getAttributes())->toArray();
                        }
                    ) ?? []);
                } else if ($request->month) {
                    $my = Carbon::parse($request->month);
                    $attendances = new Collection(Cache::remember(
                        "attendance-$user->id-$my->month-$my->year",
                        now()->addDay(),
                        function () use ($user, $my) {
                            /** @var Collection<Attendance>  */
                            $attendances = Attendance::where('user_id', $user->id)
                                ->whereMonth('date', $my->month)
                                ->whereYear('date', $my->year)
                                ->get(['id', 'status', 'date']);

                            return $attendances->map(fn ($v) => $v->getAttributes())->toArray();
                        }
                    ) ?? []);
                } else {
                    /** @var Collection */
                    $attendances = Attendance::all();
                }
                $user->attendances = $attendances;
                return $user;
            });

        $pdf = Pdf::loadView('admin.attendances.report', [
            'employees' => $employees,
            'dates' => $dates,
            'date' => $request->date,
            'month' => $request->month,
            'week' => $request->week,
            'division' => $request->division,
            'jobTitle' => $request->jobTitle,
            'start' => $request->date ? null : $start,
            'end' => $request->date ? null : $end
        ])->setPaper($request->month ? 'a3' : 'a4', $request->date ? 'portrait' : 'landscape');
        return $pdf->stream();
        // return $pdf->download();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
        //
    }
}
