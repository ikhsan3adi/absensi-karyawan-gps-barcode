<?php

namespace App\Models;

use App\Helpers;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'coordinates',
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

    protected $hidden = [
        'coordinates',
    ];

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

    function getLatLngAttribute(): array
    {
        return Helpers::unpackPoint($this->coordinates);
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
}
