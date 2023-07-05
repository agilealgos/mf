<?php

namespace CouponsPlus\App\Handlers;

use CouponsPlus\App\Conditions\BuiltIn\Conditions\CartSubtotal;
use CouponsPlus\App\Conditions\BuiltIn\Conditions\CheckoutField;
use CouponsPlus\App\Conditions\BuiltIn\Conditions\CouponUsageNumberOfTimes;
use CouponsPlus\App\Conditions\BuiltIn\Conditions\CustomUserMeta;
use CouponsPlus\App\Conditions\BuiltIn\Conditions\CustomerPurchaseHistory;
use CouponsPlus\App\Conditions\BuiltIn\Conditions\CustomerType;
use CouponsPlus\App\Conditions\BuiltIn\Conditions\Date;
use CouponsPlus\App\Conditions\BuiltIn\Conditions\GrouppedSubtotal;
use CouponsPlus\App\Conditions\BuiltIn\Conditions\Location;
use CouponsPlus\App\Conditions\BuiltIn\Conditions\PaymentMethod;
use CouponsPlus\App\Conditions\BuiltIn\Conditions\ShippingTotal;
use CouponsPlus\App\Conditions\BuiltIn\Conditions\ShippingZone;
use CouponsPlus\App\Conditions\BuiltIn\Conditions\Time;
use CouponsPlus\App\Conditions\BuiltIn\Conditions\URLParameter;
use CouponsPlus\App\Conditions\BuiltIn\Conditions\UserRegistrationTime;
use CouponsPlus\App\Conditions\BuiltIn\Conditions\UserRole;
use CouponsPlus\App\Conditions\ConditionsRegistrator;
use CouponsPlus\Original\Events\Handler\EventHandler;

Class BuiltInConditionsRegistrator extends EventHandler
{
    protected $numberOfArguments = 1;
    protected $priority = 10;

    public function execute(ConditionsRegistrator $conditionsRegistrator)
    {
        $conditionsRegistrator->register(CartSubtotal::class);
        $conditionsRegistrator->register(CheckoutField::class);
        $conditionsRegistrator->register(CouponUsageNumberOfTimes::class);
        $conditionsRegistrator->register(CustomerPurchaseHistory::class);
        $conditionsRegistrator->register(CustomerType::class);
        $conditionsRegistrator->register(CustomUserMeta::class);
        $conditionsRegistrator->register(Date::class);
        $conditionsRegistrator->register(Location::class);
        $conditionsRegistrator->register(PaymentMethod::class);
        $conditionsRegistrator->register(ShippingTotal::class);
        $conditionsRegistrator->register(ShippingZone::class);
        $conditionsRegistrator->register(Time::class);
        $conditionsRegistrator->register(URLParameter::class);
        $conditionsRegistrator->register(UserRegistrationTime::class);
        $conditionsRegistrator->register(UserRole::class);
        $conditionsRegistrator->register(GrouppedSubtotal::class);
    }
}