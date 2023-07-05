<?php

namespace CouponsPlus\App\Offers;

use CouponsPlus\App\Coupon\CartComponentsSet;
use CouponsPlus\App\Offers\Abilities\Offers;
use CouponsPlus\App\Offers\OffersScheduler;
use CouponsPlus\App\Offers\OffersSet;
use CouponsPlus\Original\Collections\Collection;

Class OffersSetCollection extends CartComponentsSet implements Offers
{
    public function apply(OffersScheduler $offersScheduler)
    {
        foreach ($this->getOffersSets()->asArray() as $offersSet) {
            $offersSet->apply($offersScheduler);
        }
    }

    public function canBeApplied() : bool
    {
        foreach ($this->getOffersSets()->asArray() as $offersSet) {
            if ($offersSet->canBeApplied()) {
                return true;
            }
        }

        return false;
    }
    
    /**
     * A collection of OffersSet objects
     */
    public function addOffersSet(OffersSet $offersSet)
    {
        $this->items->push($offersSet);
    }
 
    protected function getInitialItemsCollection(array $items) : Collection
    {
        return (new Collection($items))->filter(function($item) : bool {
            return $item instanceof OffersSet;
        });
    }
     
    public function getOffersSets() : Collection
    {
        return $this->items;
    }
}