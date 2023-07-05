<?php

namespace CouponsPlus\App\Conditions\BuiltIn\Conditions;

use Carbon\Carbon;
use CouponsPlus\App\Conditions\Condition;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\Mapper\Types;

Class CouponUsageNumberOfTimes extends Condition
{
    CONST TYPE = 'CouponUsageNumberOfTimes';
    
    protected /*WC_Coupon*/ $coupon;
    const DATABASE_DATE_FORMAT = 'Y-m-d H:i:s';

    static public function getName() : string
    {
        return __('Number Of Times Used', 'coupons-plus-international');
    }

    static public function getDescription() : string
    {
        return '';
    }
    
    // test weeks starts based on the given settings:
    // eg if setting is suday, test that week is from sunday to satirday and NOT from monday to sunday
    public static function getOptions() : Collection
    {
        return new Collection([
            'quantity' => [
                'type' => Types::STRING()->allowed([
                    'maximum'
                ])->meta([
                    'name' => __('Maximum', 'coupons-plus-international'),
                ]),
                'amount' => Types::INTEGER()->meta([
                    'name' => '',
                ]),
            ],
            'interval' => Types::STRING()->allowed([
                                            __('All Time', 'coupons-plus-international') => 'alltime', 
                                            __('Every Day', 'coupons-plus-international') => 'everyday', 
                                            __('Every Week', 'coupons-plus-international') => 'everyweek', 
                                            __('Every Month', 'coupons-plus-international') => 'everymonth', 
                                            __('Every Year', 'coupons-plus-international') => 'everyyear'
                                        ])
                                        ->withDefault('alltime')
        ]);
    }

    protected function setExtraData(\WC_Coupon $coupon)
    {
        $this->coupon = $coupon;
    }

    protected function test() : bool
    {
        (integer) $usageCount = $this->getUsageCount();

        return $usageCount < $this->options->quantity->amount;
    }

    protected function getUsageCount() : int
    {
        global $wpdb;

        return (integer) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*)
             FROM {$wpdb->posts} as posts
             JOIN {$wpdb->prefix}woocommerce_order_items as order_items
             ON order_items.order_id = posts.ID
             WHERE order_items.order_item_name = %s
             AND order_items.order_item_type = 'coupon'
             AND posts.post_date BETWEEN %s AND %s
             AND posts.post_type = 'shop_order'
            ",
            $this->coupon->get_code(),
            $startDate = $this->getDateInDATETIMEFormat()['start'],
            $endDate = $this->getDateInDATETIMEFormat()['end']
        ));
    }

    protected function getDateInDATETIMEFormat() : array
    {
        (string) $start = '';
        (string) $end = '';
        (object) $today = Carbon::now();

        $today->setWeekStartsAt((integer) get_option('start_of_week'));

        switch ($this->options->interval) {
            case 'alltime':
                $start = '1000-01-01 00:00:00';
                $end = '9999-12-31 23:59:59';
                break;
            case 'everyday':
                $start = $today->startOfDay()->format(static::DATABASE_DATE_FORMAT);
                $end = $today->endOfDay()->format(static::DATABASE_DATE_FORMAT);
                break;
            case 'everyweek':
                $start = $today->startOfWeek()->format(static::DATABASE_DATE_FORMAT);
                $end = $today->endOfWeek()->format(static::DATABASE_DATE_FORMAT);
                break;
            case 'everymonth':
                $start = $today->startOfMonth()->format(static::DATABASE_DATE_FORMAT);
                $end = $today->endOfMonth()->format(static::DATABASE_DATE_FORMAT);
                break;
            case 'everyyear':
                $start = $today->startOfYear()->format(static::DATABASE_DATE_FORMAT);
                $end = $today->endOfYear()->format(static::DATABASE_DATE_FORMAT);
                break;
        }

        return compact(
            'start',
            'end'
        );
    }
}