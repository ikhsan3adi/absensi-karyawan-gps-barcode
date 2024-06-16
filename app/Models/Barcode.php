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
    ];

    function getLatLngAttribute(): array
    {
        return Helpers::unpackPoint($this->coordinates);
    }
}
