<?php

namespace App;

use Illuminate\Support\Carbon;

class ExtendedCarbon extends Carbon
{
    /**
     * Get the year and week formatted as a string in the ISO 8601 week date format.
     *
     * @return string The formatted year-week string.
     * ```
     * '2024-W25'
     * ```
     */
    public function yearWeekString(): string
    {
        if ($this->week < 10 && !str_contains($this->week, '0')) {
            $week = "0$this->week";
        } else {
            $week = $this->week;
        }
        return "$this->year-W$week";
    }

    /**
     * Get the closest date and time from `$this` in the array of dates.
     *
     * @param array<Carbon|ExtendedCarbon|string> $dates An array of dates to compare against the target.
     * @return ExtendedCarbon|null The closest date and time from the array compared to the target.
     */
    public function closestFromDateArray(array $dates)
    {
        $closest = null;
        foreach ($dates as $date) {
            $current = ExtendedCarbon::parse($date);
            if (is_null($closest)) {
                $closest = $current;
                continue;
            }
            $closest = $this->closest($closest, $current) === $current ? $current : $closest;
        }
        return $closest;
    }
}
