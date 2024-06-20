<?php

namespace App\Http\Controllers;

use App\Helpers;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            'attachment' => ['nullable', 'file', 'max:3072'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
        ]);
        try {
            $date = date('Y-m-d');
            $existing = Attendance::where('user_id', Auth::user()->id)
                ->where('date', $date)
                ->first();

            if ($existing) {
                // Delete old attachment file if exists
                if ($existing->attachment) {
                    Storage::disk(config('jetstream.attachment_disk'))->delete($existing->attachment);
                }
                // Save new attachment file
                if ($request->file('attachment')) {
                    $newAttachment = $request->file('attachment')->storePublicly(
                        'attachments',
                        ['disk' => config('jetstream.attachment_disk')]
                    );
                }
                $existing->update([
                    'status' => $request->status,
                    'note' => $request->note,
                    'attachment' => $newAttachment ?? null,
                    'coordinates' => $request->lat ? Helpers::createPointQuery(
                        floatval($request->lat),
                        floatval($request->lng)
                    ) : null,
                ]);
            } else {
                // Save new attachment file
                if ($request->file('attachment')) {
                    $newAttachment = $request->file('attachment')->storePublicly(
                        'attachments',
                        ['disk' => config('jetstream.attachment_disk')]
                    );
                }
                Attendance::create([
                    'user_id' => Auth::user()->id,
                    'status' => $request->status,
                    'date' => $date,
                    'note' => $request->note,
                    'attachment' => $newAttachment ?? null,
                    'coordinates' => $request->lat ? Helpers::createPointQuery(
                        floatval($request->lat),
                        floatval($request->lng)
                    ) : null,
                ]);
            }

            return redirect(route('home'))
                ->with('flash.banner', __('Created successfully.'));
        } catch (\Throwable $th) {
            return redirect()->back()
                ->with('flash.banner', $th->getMessage())
                ->with('flash.bannerStyle', 'danger');
        }
    }
}
