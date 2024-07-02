<?php

namespace App;

class Helpers
{
    public static function getGoogleMapsUrl($lat, $lng)
    {
        return "https://maps.google.com/maps?q=$lat,$lng";
    }
}
