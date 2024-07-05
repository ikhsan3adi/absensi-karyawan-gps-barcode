<?php

namespace App\Models;

use App\ExtendedCarbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Attendance extends Model
{
    use HasFactory;
    use HasTimestamps;

    protected $fillable = [
        'user_id',
        'barcode_id',
        'date',
        'time_in',
        'time_out',
        'shift_id',
        'latitude',
        'longitude',
        'status',
        'note',
        'attachment',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'datetime:Y-m-d',
            'time_in' => 'datetime:H:i:s',
            'time_out' => 'datetime:H:i:s',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function barcode()
    {
        return $this->belongsTo(Barcode::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    function getLatLngAttribute(): array|null
    {
        if (is_null($this->latitude) || is_null($this->longitude)) {
            return null;
        }
        return [
            'lat' => $this->latitude,
            'lng' => $this->longitude
        ];
    }

    public static function filter(
        $date = null,
        $week = null,
        $month = null,
        $year = null,
        $userId = null,
        $division = null,
        $jobTitle = null,
        $education = null
    ) {
        return self::when($date, function (Builder $query) use ($date) {
            $query->where('date', Carbon::parse($date)->toDateString());
        })->when($week && !$date, function (Builder $query) use ($week) {
            $start = Carbon::parse($week)->startOfWeek();
            $end = Carbon::parse($week)->endOfWeek();
            $query->whereBetween('date', [$start->toDateString(), $end->toDateString()]);
        })->when($month && !$week && !$date, function (Builder $query) use ($month) {
            $date = Carbon::parse($month);
            $query->whereMonth('date', $date->month)->whereYear('date', $date->year);
        })->when($year && !$month && !$week && !$date, function (Builder $query) use ($year) {
            $date = Carbon::parse($year);
            $query->whereYear('date', $date->year);
        })->when($userId, function (Builder $query) use ($userId) {
            $query->where('user_id', $userId);
        })->when($division && !$userId, function (Builder $query) use ($division) {
            $query->whereHas('user', function (Builder $query) use ($division) {
                $query->where('division_id', $division);
            });
        })->when($jobTitle && !$userId, function (Builder $query) use ($jobTitle) {
            $query->whereHas('user', function (Builder $query) use ($jobTitle) {
                $query->where('job_title_id', $jobTitle);
            });
        })->when($education && !$userId, function (Builder $query) use ($education) {
            $query->whereHas('user', function (Builder $query) use ($education) {
                $query->where('education_id', $education);
            });
        });
    }

    public function attachmentUrl(): ?Attribute
    {
        if (!$this->attachment) {
            return null;
        }

        return Attribute::get(function (): string {
            if (str_contains($this->attachment, 'https://') || str_contains($this->attachment, 'http://')) {
                return $this->attachment;
            }
            return Storage::disk(config('jetstream.attachment_disk'))->url($this->attachment);
        });
    }

    public static function clearUserAttendanceCache(Authenticatable $user, Carbon $date)
    {
        if (is_null($user)) return false;
        $date = new ExtendedCarbon($date);
        $monthYear = "$date->month-$date->year";
        $week = $date->yearWeekString();
        $ymd = $date->format('Y-m-d');

        try {
            Cache::forget("attendance-$user->id-$monthYear");
            Cache::forget("attendance-$user->id-$week");
            Cache::forget("attendance-$user->id-$ymd");
            return true;
        } catch (\Throwable $_) {
            return false;
        }
    }
}
