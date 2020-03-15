<?php

namespace App\Helpers;

use Carbon\Carbon;

class Time
{
    /**
     * Get range
     *
     * @param ['from', 'to'] $firstRange
     * @param ['from', 'to'] $secondRange
     *
     */
    public static function getRange($firstRange, $secondRange)
    {
        list($firstFrom, $firstTo) = static::parseToCarbon($firstRange);
        list($secondFrom, $secondTo) = static::parseToCarbon($secondRange);
        $from = static::maxDate($firstFrom, $secondFrom);
        $to = static::minDate($firstTo, $secondTo);

        return $from->lte($to) ? array($from, $to->endOfDay()) : null;
    }

    /**
     * Get range to get used resource
     *
     * @param ['from', 'to'] $range
     *
     */
    public static function getCurrentRange($range)
    {
        list($from, $to) = static::parseToCarbon($range);
        $now = Carbon::now();
        $from = static::minDate($from, $now);
        $to = static::minDate($to, $now);

        return array($from, $to->endOfDay());
    }

    /**
     * @param ['from', 'to'] $range
     *
     */
    public static function parseToCarbon($range)
    {
        $from = Carbon::parse($range[0]);
        $to = Carbon::parse($range[1]);
        return array($from, $to);
    }

    /**
     * @param Date $firstDate
     * @param Date $secondDate
     *
     */
    protected static function minDate($firstDate, $secondDate)
    {
        return $firstDate->lte($secondDate) ? $firstDate : $secondDate;
    }

    /**
     * @param Date $firstDate
     * @param Date $secondDate
     *
     */
    protected static function maxDate($firstDate, $secondDate)
    {
        return $firstDate->gte($secondDate) ? $firstDate : $secondDate;
    }
}
