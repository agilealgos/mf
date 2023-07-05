<?php

namespace CouponsPlus\App\Conditions\BuiltIn\Conditions;

use Carbon\Carbon;
use CouponsPlus\App\Conditions\Condition;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\Mapper\Types;

Class Date extends Condition
{
    const TYPE = 'Date';
    
    static public function getName() : string
    {
        return __('Date', 'coupons-plus-international');
    }

    static public function getDescription() : string
    {
        return '';
    }
    
    public static function getOptions() : Collection
    {
        return new Collection([
            'type' => Types::STRING()->allowed([
                __('Day', 'coupons-plus-international') => 'exact', 
                __('Range', 'coupons-plus-international') => 'range',
            ]),
            'date' => [
                'exact' => Types::STRING(), // hour is ignored
                'from' => Types::STRING(), // hours are considered!
                'to' => Types::STRING()  // hours are considered!
            ]
        ]);
    }
    
    protected function test() : bool
    {
        (object) $today = Carbon::now();

        switch ($this->options->type) {
            case 'exact':
                return $this->matchExactDates($today);
                break;
            case 'range':
                return $this->matchDateInRange($today);
                break;
        }
    }

    protected function matchExactDates(Carbon $today) : bool
    {
        (object) $expectedDate = new Carbon($this->options->date->exact->get());

        return $today->startOfDay()->equalTo($expectedDate->startOfDay());
    }

    protected function matchDateInRange(Carbon $today) : bool
    {
        (object) $start = new Carbon($this->options->date->from->get());
        (object) $end = new Carbon($this->options->date->to->get());

        return $today->greaterThanOrEqualTo($start) &&
               $today->lessThanOrEqualTo($end);
    }
}