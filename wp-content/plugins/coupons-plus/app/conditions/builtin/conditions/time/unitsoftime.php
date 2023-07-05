<?php

namespace CouponsPlus\App\Conditions\BuiltIn\Conditions\Time;

use Carbon\Carbon;
use CouponsPlus\App\Time\WpDateTimeZone;
use CouponsPlus\Original\Collections\Collection;

Class UnitsOfTime
{
    public function getAll() : Collection
    {
        return new Collection([
            'hours' => $this->getHours(),
            'daysofweek' => $this->getDaysOfWeek(),
            'daysofmonth' => $this->getDaysOfMonth(),
            'months' => $this->getMonths(),
            'years' => $this->getYears(),
        ]);   
    }
    
    public function getHours() : Collection
    {
        return new Collection([
            __('1 A.M.', 'coupons-plus-international') => 1,
            __('2 A.M.', 'coupons-plus-international') => 2,
            __('3 A.M.', 'coupons-plus-international') => 3,
            __('4 A.M.', 'coupons-plus-international') => 4,
            __('5 A.M.', 'coupons-plus-international') => 5,
            __('6 A.M.', 'coupons-plus-international') => 6,
            __('7 A.M.', 'coupons-plus-international') => 7,
            __('8 A.M.', 'coupons-plus-international') => 8,
            __('9 A.M.', 'coupons-plus-international') => 9,
            __('10 A.M.', 'coupons-plus-international') => 10,
            __('11 A.M.', 'coupons-plus-international') => 11,
            __('12 P.M.', 'coupons-plus-international') => 12,
            __('1 P.M.', 'coupons-plus-international') => 13,
            __('2 P.M.', 'coupons-plus-international') => 14,
            __('3 P.M.', 'coupons-plus-international') => 15,
            __('4 P.M.', 'coupons-plus-international') => 16,
            __('5 P.M.', 'coupons-plus-international') => 17,
            __('6 P.M.', 'coupons-plus-international') => 18,
            __('7 P.M.', 'coupons-plus-international') => 19,
            __('8 P.M.', 'coupons-plus-international') => 20,
            __('9 P.M.', 'coupons-plus-international') => 21,
            __('10 P.M.', 'coupons-plus-international') => 22,
            __('11 P.M.', 'coupons-plus-international') => 23,
            __('12 A.M.', 'coupons-plus-international') => 0,
        ]);
    }
 
    public function getDaysOfWeek() : Collection
    {
        return new Collection([
            __('Sunday', 'coupons-plus-international') => Carbon::SUNDAY,
            __('Monday', 'coupons-plus-international') => Carbon::MONDAY,
            __('Tuesday', 'coupons-plus-international') => Carbon::TUESDAY,
            __('Wednesday', 'coupons-plus-international') => Carbon::WEDNESDAY,
            __('Thursday', 'coupons-plus-international') => Carbon::THURSDAY,
            __('Friday', 'coupons-plus-international') => Carbon::FRIDAY,
            __('Saturday', 'coupons-plus-international') => Carbon::SATURDAY,
        ]);
    }
          
    public function getDaysOfMonth() : Collection
    {
        return Collection::range(1, 31)->mapWithKeys(function(int $day, int $index) : array {
            return [
                'key' => $day,
                'value' => $day
            ];
        });
    }

    public function getMonths() : Collection
    {
        return new Collection([
            __('January', 'coupons-plus-international') => 1,
            __('February', 'coupons-plus-international') => 2,
            __('March', 'coupons-plus-international') => 3,
            __('April', 'coupons-plus-international') => 4,
            __('May', 'coupons-plus-international') => 5,
            __('June', 'coupons-plus-international') => 6,
            __('July', 'coupons-plus-international') => 7,
            __('August', 'coupons-plus-international') => 8,
            __('September', 'coupons-plus-international') => 9,
            __('October', 'coupons-plus-international') => 10,
            __('November', 'coupons-plus-international') => 11,
            __('December', 'coupons-plus-international') => 12,
        ]);
    }

    public function getYears() : Collection
    {
        (integer) $currentYear = Carbon::now(WpDateTimeZone::getWpTimezone())->year;

        return Collection::range(
            $start = $currentYear,
            $end = $currentYear + 20
        )->mapWithKeys(function(int $day, int $index) : array {
            return [
                'key' => $day,
                'value' => $day
            ];
        });
    }
}