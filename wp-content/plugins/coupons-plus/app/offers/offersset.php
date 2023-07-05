<?php

namespace CouponsPlus\App\Offers;

use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\App\Coupon\CartComponentsSet;
use CouponsPlus\App\Offers\Abilities\Offers;
use CouponsPlus\App\Offers\Offer;
use CouponsPlus\App\Offers\OffersScheduler;
use CouponsPlus\Original\Collections\Collection;
use WC_Coupon;

Class OffersSet extends CartComponentsSet implements Offers
{
    protected $itemsSet;

    public static function createFromData(array $offersData, WC_Coupon $coupon) : OffersSet
    {
        return new static(Collection::create($offersData)->map(function($offerData) use ($coupon) : Offer {
            return Offer::createFromOptions($offerData, $coupon);
        })->asArray());
    }

    public function __construct(...$parameters)
    {
        $this->itemsSet = new ItemsSet([]);

        parent::__construct(...$parameters);
    }

    public function apply(OffersScheduler $offersScheduler)
    {
        foreach ($this->items->asArray() as $offer) {
            if ($offer->needsToBeRescheduled($this->getItemsSet()) && $offersScheduler->offerHasBeenApplied($offer, $this->getItemsSet())) {
                continue;
            }

            if ($offer->needsToBeRescheduled($this->getItemsSet())) {
                $offersScheduler->schedule($offer, $this->getItemsSet());
            } else {
                $offer->apply($this->getItemsSet());
            }
        }
    }

    public function getItemsSet() : ItemsSet
    {
        return $this->itemsSet;
    }

    public function canBeApplied() : bool
    {
        return $this->getItemsSet()->getItems()->haveAny();
    }

    protected function getInitialItemsCollection(array $items) : Collection
    {
        return (new Collection($items))->filter(function($offer) {
            return $offer instanceof Offer;
        });
    }
}