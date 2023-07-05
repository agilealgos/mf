<?php

namespace CouponsPlus\App\Offers;

use CouponsPlus\App\Conditions\CardExportableData;
use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\App\Coupon\Abilities\DashboardCard;
use CouponsPlus\App\Offers\Abilities\SchedulableOffer;
use CouponsPlus\App\Offers\OffersRegistrator;
use CouponsPlus\Original\Cache\MemoryCache;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\JSONMapper;
use CouponsPlus\Original\Collections\MappedObject;
use CouponsPlus\Original\Collections\Mapper\Types;
use WC_Cart;
use WC_Coupon;

Abstract Class Offer implements DashboardCard
{
    protected /*WC_Cart*/ $cart;
    protected /*WC_Coupon*/ $coupon;
    protected /*MappedObject*/ $options;

    abstract public static function getOptions() : Collection;
    abstract public function apply(ItemsSet $itemsSet);
    abstract static public function getIconUrl() : string;
    /*abstract*/ protected function onOptionsLoaded() {} #overridable iff needed

    public static function createFromOptions($options, WC_Coupon $coupon) : Offer
    {
        (object) $JSONMapper = new JSONMapper([
            'type' => Types::STRING()->allowed(OffersRegistrator::get()->all()->map(function(string $Offer) : string {
                return $Offer::TYPE;
            })),
            'options' => Types::ANY
        ]);

        (object) $preOptions = $JSONMapper->smartMap($options);

        (string) $Offer = OffersRegistrator::get()->all()->get($preOptions->type);

        (object) $offer = new $Offer($preOptions->options);

        $offer->setCoupon($coupon);
        $offer->setCart(\WC()->cart);

        return $offer;
    }
    
    public function __construct(/*string|array*/ $options)
    {
        (object) $JSONMapper = new JSONMapper($this->getOptions()->asArray());

        $this->options = $JSONMapper->smartMap($options);
        $this->cache = new MemoryCache;

        $this->onOptionsLoaded();
    }

    public function needsToBeRescheduled(ItemsSet $itemsSet) : bool
    {
        return $this instanceof SchedulableOffer && $this->canBeScheduled($itemsSet);
    }

    public function setCoupon(\WC_Coupon $coupon)
    {
        $this->coupon = $coupon;
    }

    public function setCart(WC_Cart $cart = null)
    {
        $this->cart = $cart; 
    }

    public function couponIsActive()
    {
        return in_array(
            $this->coupon->get_code($context = 'edit'), 
            $this->cart->get_applied_coupons()
        );
    }

    public static function exportDefault() : MappedObject
    {
        (object) $offer = new static([]);

        return $offer->options;
    }

    public function getLoadedOptions() : MappedObject
    {
        return $this->options;   
    }
    
    public function getDataToExport()
    {
        return new CardExportableData($this);
    }
}