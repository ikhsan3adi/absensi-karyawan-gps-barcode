<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class UserAttendanceController extends Controller
{
    public function applyLeave()
    {
        $attendance = Attendance::where('user_id', Auth::user()->id)
            ->where('date', date('Y-m-d'))
            ->first();
        return view('attendances.apply-leave', ['attendance' => $attendance]);
    }

    public function storeLeaveRequest(Request $request)
    {
        $request->validate([
            'status' => ['required', 'in:excused,sick'],
            'note' => ['required', 'string', 'max:255'],
            'from' => ['required', 'date'],
            'to' => ['nullable', 'date', 'after:from'],
            'attachment' => ['nullable', 'file', 'max:3072'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
        ]);
        try {
            // Save new attachment file
            $newAttachment = null;
            if ($request->file('attachment')) {
                $newAttachment = $request->file('attachment')->storePublicly(
                    'attachments',
                    ['disk' => config('jetstream.attachment_disk')]
                );
            }

            $fromDate = Carbon::parse($request->from);
            $fromDate->range($toDate = Carbon::parse($request->to ?? $fromDate))
                ->forEach(function (Carbon $date) use ($request, $newAttachment) {
                    $existing = Attendance::where('user_id', Auth::user()->id)
                        ->where('date', $date->format('Y-m-d'))
                        ->first();

                    if ($existing) {
                        $existing->update([
                            'status' => $request->status,
                            'note' => $request->note,
                            'attachment' => $newAttachment ?? $existing->attachment,
                            'latitude' => doubleval($request->lat) ?? $existing->latitude,
                            'longitude' => doubleval($request->lng) ?? $existing->longitude,
                        ]);
                    } else {
                        Attendance::create([
                            'user_id' => Auth::user()->id,
                            'status' => $request->status,
                            'date' => $date->format('Y-m-d'),
                            'note' => $request->note,
                            'attachment' => $newAttachment ?? null,
                            'latitude' => $request->lat ? doubleval($request->lat) : null,
                            'longitude' => $request->lng ? doubleval($request->lng) : null,
                        ]);
                    }
                });

            Attendance::clearUserAttendanceCache(Auth::user(), $fromDate);
            if (!$fromDate->isSameMonth($toDate)) {
                Attendance::clearUserAttendanceCache(Auth::user(), $toDate);
            }

            return redirect(route('home'))
                ->with('flash.banner', __('Created successfully.'));
        } catch (\Throwable $th) {
            return redirect()->back()
                ->with('flash.banner', $th->getMessage())
                ->with('flash.bannerStyle', 'danger');
        }
    }

    public function history()
    {
        return view('attendances.history');
    }
}
