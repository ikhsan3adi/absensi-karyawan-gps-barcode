<?php

namespace App;

use Illuminate\Support\Facades\DB;

class Helpers
{
    public static function createPointQuery(float $lat, float $lng)
    {
        $sql = "ST_GeomFromText('POINT($lat $lng)', 4326)";
        return DB::raw($sql);
    }

    /**
     * Unpacks the given point into individual components.
     *
     * @param mixed $point The point to unpack.
     * @return array The unpacked point in the format:
     * ```
     * [
     *   ...
     *   'lat' => latitude,
     *   'lng' => longitude
     * ]
     * ```
     */
    public static function unpackPoint($point = null)
    {
        if (is_null($point)) {
            return ['lat' => null, 'lng' => null];
        }
        return unpack('x/x/x/x/corder/Ltype/dlng/dlat', $point);
    }

    public static function getGoogleMapsUrl($lat, $lng)
    {
        return "https://maps.google.com/maps?q=$lat,$lng";
    }

    /**
     * Get the year and week formatted as a string in the ISO 8601 week date format.
     *
     * @param string|int $week The week number to format.
     * @param string|int $year The year to format.
     * @return string The formatted year-week string.
     */
    public static function yearWeekString(string|int $week, string|int $year): string
    {
        if ($week < 10 && !str_contains($week, '0')) {
            $week = "0$week";
        }
        return "$year-W$week";
    }
}
