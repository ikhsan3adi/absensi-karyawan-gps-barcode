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
     *               [
     *                   ...
     *                   'lat' => latitude,
     *                   'lng' => longitude
     *               ]
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
}
