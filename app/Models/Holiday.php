<?php

namespace App\Models;

use Carbon\Carbon;

class Holiday
{
    // Static data for the meantime as there are no database table for Philippines holidays might be migrated in the Database in the future
    // as ILOILO has also a specific Holiday
    // Default holidays property
    protected static $defaultHolidays = [];

    /**
     * Initialize the default holidays.
     */
    protected static function initializeDefaultHolidays()
    {
        self::$defaultHolidays = [
            //THIS IS BASED ON MORE POWER HOLIDAY CALENDAR FOR 2024 (YYYY-MM-DD)
            ['date' => date('Y') . '-01-01', 'name' => 'New Year\'s Day', 'type' => 'Regular Holiday'],
            ['date' => date('Y') . '-02-10', 'name' => 'Chinese New Year', 'type' => 'Regular Holiday'],
            ['date' => date('Y') . '-03-28', 'name' => 'Maundy Thursday', 'type' => 'Regular Holiday'],
            ['date' => date('Y') . '-03-29', 'name' => 'Good Friday', 'type' => 'Regular Holiday'],
            ['date' => date('Y') . '-03-30', 'name' => 'Black Saturday', 'type' => 'Regular Holiday'],
            ['date' => date('Y') . '-04-09', 'name' => 'Araw ng Kagitingan', 'type' => 'Regular Holiday'],
            ['date' => date('Y') . '-05-01', 'name' => 'Labor Day', 'type' => 'Regular Holiday'],
            ['date' => date('Y') . '-06-12', 'name' => 'Independence Day', 'type' => 'Regular Holiday'],
            ['date' => date('Y') . '-06-17', 'name' => 'Eid`l Adha', 'type' => 'Regular Holiday'],
            ['date' => date('Y') . '-08-21', 'name' => 'Ninoy Aquino Day', 'type' => 'Regular Holiday'],
            ['date' => date('Y') . '-08-26', 'name' => 'National Heroes Day', 'type' => 'Regular Holiday'],
            ['date' => date('Y') . '-11-01', 'name' => 'All Saint`s Day', 'type' => 'Regular Holiday'],
            ['date' => date('Y') . '-11-02', 'name' => 'All Soul`s Day', 'type' => 'Regular Holiday'],
            ['date' => date('Y') . '-11-30', 'name' => 'Bonifacio Day', 'type' => 'Regular Holiday'],
            ['date' => date('Y') . '-12-08', 'name' => 'Feast of the Immaculate Conception of Mary', 'type' => 'Regular Holiday'],
            ['date' => date('Y') . '-12-24', 'name' => 'Christmas Eve', 'type' => 'Regular Holiday'],
            ['date' => date('Y') . '-12-25', 'name' => 'Christmas Day', 'type' => 'Regular Holiday'],
            ['date' => date('Y') . '-12-30', 'name' => 'Rizal Day', 'type' => 'Regular Holiday'],
            ['date' => date('Y') . '-12-31', 'name' => 'Last Day of the year', 'type' => 'Regular Holiday'],
        ];
    }

    /**
     * Get a list of holidays in the Philippines.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getPhilippineHolidays()
    {
        // Initialize default holidays if not done already
        if (empty(self::$defaultHolidays)) {
            self::initializeDefaultHolidays();
        }

        return collect(self::$defaultHolidays);
    }

    /**
     * Check if a given date is a holiday.
     *
     * @param string $date
     * @return bool
     */
    public static function isHoliday($date)
    {
        $holidays = self::getPhilippineHolidays();
        return $holidays->contains('date', $date);
    }
}
