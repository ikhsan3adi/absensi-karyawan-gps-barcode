<?php

namespace App;

class Helpers
{
    public static function getGoogleMapsUrl($lat, $lng)
    {
        return "https://maps.google.com/maps?q=$lat,$lng";
    }

    /**
     * Get the URL path from the app URL
     *
     * E.g. base url/app url = http://localhost:8000/path => path
     *
     * Returns empty string if base url is root path
     */
    public static function getNonRootBaseUrlPath()
    {
        $segments = explode('/', parse_url(config('app.url'), PHP_URL_PATH));
        return count($segments) < 2 ? '' : $segments[1];
    }
}
