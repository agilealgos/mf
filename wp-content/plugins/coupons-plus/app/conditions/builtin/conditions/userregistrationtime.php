<?php

namespace CouponsPlus\App\Conditions\BuiltIn\Conditions;

use Carbon\Carbon;
use CouponsPlus\App\Conditions\Condition;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\Mapper\Types;

Class UserRegistrationTime extends Condition
{
    const TYPE = 'UserRegistrationTime';
    
    static public function getName() : string
    {
        return __('Account Registration Date', 'coupons-plus-international');
    }

    static public function getDescription() : string
    {
        return '';
    }
    
    public static function getOptions() : Collection
    {
        return new Collection([
            'type' => Types::STRING()->allowed([
                                        __('Time', 'coupons-plus-international') => 'recently', 
                                        __('In Range', 'coupons-plus-international') => 'range',
                                       ])
                                      ->withDefault('recently'),
            'recently' => [
                'value' => Types::INTEGER,
                'unit' => Types::STRING()->withDefault('hours')
                                         ->allowed([
                                            __('Hours Ago', 'coupons-plus-international') => 'hours', 
                                            __('Days Ago', 'coupons-plus-international') => 'days',
                                          ]),
            ],
            'range' => [
                'from' => Types::STRING()->meta([
                    'name' => __('From', 'coupons-plus-international')
                ]),
                'to' => Types::STRING()->meta([
                    'name' => __('To', 'coupons-plus-international')
                ])
            ]
        ]);
    }
    
    protected function test() : bool
    {
        (object) $userRegistrationTime = new Carbon(
            (new \WC_Customer(get_current_user_id()))->get_date_created($context = 'edit')
        );

        switch ($this->options->type) {
            case 'recently':
                return $this->matchRecently($userRegistrationTime);
            break;
            case 'range':
                return $this->matchRange($userRegistrationTime);
            break;
        }
    }

    protected function matchRecently(Carbon $userRegistrationTime) : bool
    {
        (object) $now = Carbon::now();
        (object) $oldestAllowedTime = $now;

        switch ($this->options->recently->unit) {
            case 'hours':
                $oldestAllowedTime = $now->subHours($this->options->recently->value);
                break;
            case 'days':
                $oldestAllowedTime = $now->subDays($this->options->recently->value);
                break;
        }

        return $userRegistrationTime->greaterThanOrEqualTo($oldestAllowedTime);
    }

    protected function matchRange(Carbon $userRegistrationTime) : bool
    {
        (object) $start = new Carbon($this->options->range->from->get());
        (object) $end = new Carbon($this->options->range->to->get());

        return $userRegistrationTime->greaterThanOrEqualTo($start) &&
               $userRegistrationTime->lessThanOrEqualTo($end);
    }
}