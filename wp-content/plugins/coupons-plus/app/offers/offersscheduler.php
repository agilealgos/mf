<?php

namespace CouponsPlus\App\Offers;

use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\App\Offers\Offer;
use CouponsPlus\Original\Collections\Collection;

Class OffersScheduler
{
    protected $scheduledOffers;
    protected $scheduledOffersApplied;

    public function __construct()
    {
        $this->reset();
    }

    public function __destruct()
    {
        $this->destroy();   
    }

    public function registerEvents()
    {
        // this action will get triggered when calling recalculateTotalsIfPendingOffers()
        add_action('woocommerce_before_calculate_totals', [$this, 'applyScheduledOffers']);  
        add_action('woocommerce_after_calculate_totals', [$this, 'recalculateTotalsIfPendingOffers']);
        add_action('woocommerce_cart_item_removed', [$this, 'reset']);
    }

    public function recalculateTotalsIfPendingOffers()
    {
        if ($this->scheduledOffers->haveAny()) {
            \WC()->cart->calculate_totals();
        }
    }

    public function applyScheduledOffers()
    {
        $this->apply();   
    }
    
    public function schedule(Offer $offer, ItemsSet $itemsSet)
    {
        $this->scheduledOffers->push(
            new Collection([
                'id' => $offer->getScheduleId($itemsSet), 
                'offer' => $offer,
                'itemsSet' => $itemsSet,
            ])
        );
    }

    public function apply()
    {
        (object) $scheduledOffers = $this->scheduledOffers->asArray();

        // very important since offers might trigger a recalculation of 
        // the cart, we don't want to end up with an endless loop.
        $this->reset();

        foreach ($scheduledOffers as $scheduledOffer) {
            (object) $offer = $scheduledOffer->get('offer');

            $this->registerAppliedOffer($offer, $scheduledOffer->get('itemsSet'));

            $offer->apply($scheduledOffer->get('itemsSet'));
        }   
    }

    public function offerHasBeenApplied(Offer $offer, ItemsSet $itemsSet) : bool
    {
        return $this->scheduledOffersApplied->hasKey($offer->getScheduleId($itemsSet));
    }
 
    protected function registerAppliedOffer(Offer $offer, ItemsSet $itemsSet)
    {
        $this->scheduledOffersApplied->add(
            $offer->getScheduleId($itemsSet),
            $offer->getScheduleId($itemsSet)
        );
    }
    
    public function reset()
    {
        $this->scheduledOffers = new Collection([]);   
        $this->scheduledOffersApplied = new Collection([]);
    }   

    public function destroy()
    {
        $this->reset();
        remove_action('woocommerce_before_calculate_totals', [$this, 'applyScheduledOffers']);  
        remove_action('woocommerce_after_calculate_totals', [$this, 'recalculateTotalsIfPendingOffers']);
    }
}