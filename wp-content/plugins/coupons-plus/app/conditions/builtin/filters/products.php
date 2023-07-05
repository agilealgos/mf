<?php

namespace CouponsPlus\App\Conditions\BuiltIn\Filters;

use CouponsPlus\App\Conditions\CartItem;
use CouponsPlus\App\Conditions\Filter;
use CouponsPlus\App\Conditions\IntersectionFilter;
use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\Mapper\Types;
use CouponsPlus\Original\Environment\Env;

Class Products extends Filter
{
    const TYPE = 'Products';
    const SET = IntersectionFilter::class;
    
    public static function getName() : string
    {
        return __('Products', 'coupons-plus-international');
    }

    public static function getDescription() : string
    {
        return '';
    }

    public static function getOptions() : Collection
    {
        return new Collection([
            'ids' => Types::COLLECTION(),
            'inclusionType' => Types::STRING()->withDefault('allowed')
                                              ->allowed([
                                                  __('Allowed', 'coupons-plus-international') => 'allowed', 
                                                  __('Forbidden', 'coupons-plus-international') => 'forbidden',
                                              ])
        ]);   
    }

    public function onOptionsLoaded()
    {
        do_action(Env::getWithPrefix('products_loaded_ids'), $this->getProductIdsData());
    }

    public function filterSet(ItemsSet $itemsSet) : ItemsSet
    {
        /**
         * 'ids' => [
                ['id' => 100, 'variationIDs' => [110, 130]]
            ],
         */
        /*
        +
        + If it's a variable product and no variation ids have been specified (emty array),
        + then ANY variation will be accepted.
        +
        +*/
        return new ItemsSet($itemsSet->getItems()->filter(function(CartItem $cartItem) : bool {
            if ($cartItem->getProduct()->get_type() === 'variation') {
                (boolean) $productWasFound = (boolean) $this->getProductIdsData()->find(function(array $idData) use ($cartItem) /*: ?int*/ {
                    (boolean) $mainProductIdMatches = $idData['id'] === $cartItem->getProduct()->get_parent_id($context = 'edit');

                    (boolean) $variationIDMatches = in_array($cartItem->getProduct()->get_id(), $idData['variationIDs']);
                    (boolean) $hasNotSpecifiedAVariationSoANYVariationShouldPass = empty($idData['variationIDs']);

                    return $mainProductIdMatches 
                            && 
                           ($variationIDMatches || $hasNotSpecifiedAVariationSoANYVariationShouldPass);
                });
            } else {
                (boolean) $productWasFound = (boolean) $this->getProductIdsData()->map(function(array $idData) /*: ?int*/ {
                    return $idData['id'];
                })->contain($cartItem->getProduct()->get_id());
            }
            return $this->options->inclusionType->is('allowed') ? $productWasFound : !$productWasFound;
        })->asArray());
    }

    protected function getProductIdsData() : Collection
    {
        return $this->cache->getIfExists('productIds')->otherwise(function() : Collection {

            (array) $idDataTemplate = ['id' => null, 'variationIDs' => [/*110, 130*/]];
            // we'll make sure it's the right format.
            return $this->options->ids->map(function(/*array*/$idData) use ($idDataTemplate) : array {
                (array) $ids = array_merge($idDataTemplate, (array) $idData);

                $ids['id'] = (integer) $ids['id'];
                $ids['variationIDs'] = (array) $ids['variationIDs'];

                return $ids;
            });
        });
    }
}