<?php

namespace App\Models;

use App\Helpers;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barcode extends Model
{
    use HasFactory;
    use HasTimestamps;

    protected $fillable = [
        'name',
        'value',
        'coordinates',
        'radius',
        'time_limit',
        // 'time_in_valid_from',
        // 'time_in_valid_until',
        // 'time_out_valid_from',
        // 'time_out_valid_until',
    ];

    function getLatLngAttribute(): array
    {
        return Helpers::unpackPoint($this->coordinates);
    }
}
