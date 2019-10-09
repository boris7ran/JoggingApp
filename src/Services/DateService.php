<?php

namespace App\Services;

use DateTime;
use Exception;

class DateService
{
    /**
     * @param DateTime $date
     * 
     * @return int
     */
    public static function getWeek(DateTime $date): int
    {
        $week = (int)$date->format('W');

        return $week;
    }

    /**
     * @param DateTime $date
     *
     * @return DateTime
     *
     * @throws Exception
     */
    public static function getStartDate(DateTime $date): DateTime
    {
        $temp_date = new DateTime();
        $week = (int)$date->format('W');
        $year = (int)$date->format('Y');
        $temp_date->setISODate($year, $week);

        return $temp_date;
    }

    /**
     * @param DateTime $date
     *
     * @return DateTime
     *
     * @throws Exception
     */
    public static function getEndDate(DateTime $date): DateTime
    {
        $temp_date = new DateTime();
        $week = (int)$date->format('W');
        $year = (int)$date->format('Y');
        $temp_date->setISODate($year, $week, 7);

        return $temp_date;
    }
}