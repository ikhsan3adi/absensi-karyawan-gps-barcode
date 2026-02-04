<?php
namespace App\Traits;

use Illuminate\Support\Carbon;

trait SafeDateParser
{
    /**
     * Safely parse a date string.
     *
     * @param mixed $value
     * @param mixed $fallback
     * @return \Illuminate\Support\Carbon|mixed
     */
    public function safeParseDate($value, $fallback = null)
    {
        if (empty($value)) {
            return $fallback;
        }

        try {
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return $fallback;
        }
    }
}
