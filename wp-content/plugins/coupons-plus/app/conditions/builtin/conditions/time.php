<?php

namespace CouponsPlus\App\Conditions\BuiltIn\Conditions;

use Carbon\Carbon;
use CouponsPlus\App\Conditions\Condition;
use CouponsPlus\App\Time\WpDateTimeZone;
use CouponsPlus\App\Validators\InclusionValueValidator;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\Mapper\Types;

Class Time extends Condition
{
    const TYPE = 'Time';
    
    static public function getName() : string
    {
        return __('Time', 'coupons-plus-international');
    }

    static public function getDescription() : string
    {
        return '';
    }
    
    public static function getOptions() : Collection
    {
        return new Collection([
            'unit' => [
                'type' => Types::STRING()->allowed([
                    __('Hours', 'coupons-plus-international') => 'hours', 
                    __('Days of Week', 'coupons-plus-international') => 'daysofweek', 
                    __('Days of Month', 'coupons-plus-international') => 'daysofmonth', 
                    __('Months', 'coupons-plus-international') => 'months', 
                    __('Years', 'coupons-plus-international') => 'years',

                    // days of week: mon, tue, wed, etc
                    // days of month: 1st , 21st, 3rd, etc
                    // example 2x1 pizza tuesdays only
                    // promo valid throught december
                ])->withDefault('months'),
                'values' => Types::COLLECTION
             ],
            'inclusionType' => InclusionValueValidator::getOptions()->get('inclusionType')
        ]);
    }

    protected function test() : bool
    {
        (object) $inclusionValidator = new InclusionValueValidator([
            'expectedValues' => $this->options->unit->values->asArray(),
            'inclusionType' => $this->options->inclusionType
        ]);

        $inclusionValidator->setValueToValidate($this->getCurrentTimeFormatted());

        return $inclusionValidator->isValid();
    }

    /**
     * DO NOT CREATE CARBON INSTANCES DIRECTLY, USE THIS METHOD INSTEAD
     */
    public function getCurrentDate() : Carbon
    {
        return Carbon::now(WpDateTimeZone::getWpTimezone());
    }

    protected function getCurrentTimeFormatted() : string
    {
        (object) $time = $this->getCurrentDate();

        switch ($this->options->unit->type) {
            case 'hours':
                return $this->getCurrentHourFormatted($time);
            break;
            case 'daysofweek':
                return $this->getCurrentDayOfWeekFormatted($time);
            break;
            case 'daysofmonth':
                return $this->getCurrentDayOfMonthFormatted($time);
            break;
            case 'months':
                return $this->getCurrentMonthFormatted($time);
            break;
            case 'years':
                return $this->getCurrentYearFormatted($time);
            break;
        }   
    }
    
    protected function getCurrentHourFormatted(Carbon $time) : int
    {
        return $time->hour;
    }

    protected function getCurrentDayOfWeekFormatted(Carbon $time) : int
    {
        return $time->dayOfWeek;
    }

    protected function getCurrentDayOfMonthFormatted(Carbon $time) : int
    {
        return $time->day;
    }

    protected function getCurrentMonthFormatted(Carbon $time) : int
    {
        return $time->month;
    }

    protected function getCurrentYearFormatted(Carbon $time) : int
    {
        return $time->year;
    }
}