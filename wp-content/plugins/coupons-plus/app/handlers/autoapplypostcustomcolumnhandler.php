<?php

namespace CouponsPlus\App\Handlers;

use CouponsPlus\App\Coupon\Coupon;
use CouponsPlus\App\Coupon\CouponOffersTracker;
use CouponsPlus\Original\Environment\Env;
use CouponsPlus\Original\Events\Handler\EventHandler;

Class AutoApplyPostCustomColumnHandler extends EventHandler
{
    protected $numberOfArguments = 1;
    protected $priority = 10;

    public function execute()
    {
        add_action('manage_shop_coupon_posts_columns', [$this, 'registerColumn'], 10000);
        add_action('manage_shop_coupon_posts_custom_column', [$this, 'renderColumn'], 10, 2);
    }

    public function registerColumn(array $columns) : array
    {
        $columns[$this->getCoumnId()] = __('Apply Type', 'coupons-plus-international');

        return $columns;
    }

    public function renderColumn(string $columnName, int $couponId)
    {
        if ($columnName !== $this->getCoumnId()) {
            return;
        }

        (object) $coupon = new Coupon(
            new \WC_Coupon($couponId), 
            new CouponOffersTracker
        );

        if ($coupon->canBeAutoApplied()) {
            (string) $auto = __('Auto', 'coupons-plus-international');
            print "<span class=\"cp-auto-apply-enabled\">{$auto}</span>";
        } else {
            print __('Manual', 'coupons-plus-international');
        }
    }

    protected function getCoumnId() : string
    {
        return Env::getWithPrefix('auto_apply');
    }
}