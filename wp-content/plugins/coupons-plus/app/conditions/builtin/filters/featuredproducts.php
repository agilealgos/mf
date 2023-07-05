<?php

namespace CouponsPlus\App\Conditions\BuiltIn\Filters;

use CouponsPlus\App\Conditions\CartItem;
use CouponsPlus\App\Conditions\Filter;
use CouponsPlus\App\Conditions\IntersectionFilter;
use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\App\Validators\InclusionValueValidator;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\Mapper\Types;

Class FeaturedProducts extends Filter
{
    const TYPE = 'FeaturedProducts';
    const SET = IntersectionFilter::class;
    
    public static function getName() : string
    {
        return __('Featured Products', 'coupons-plus-international');
    }

    public static function getDescription() : string
    {
        return '';
    }

    public static function getOptions() : Collection
    {
        return new Collection([
            'type' => Types::STRING()->withDefault('include')
                                     ->allowed([
                                        __('Only Featured Products', 'coupons-plus-international') => 'include', 
                                        __('Not Featured Products', 'coupons-plus-international') => 'exclude',
                                      ])
        ]);   
    }

    public function filterSet(ItemsSet $itemsSet) : ItemsSet
    {
        return new ItemsSet($itemsSet->getItems()->filter(function(CartItem $cartItem) : bool {
            (boolean) $isFeatured = $cartItem->getProduct()->get_featured($context = 'edit');

            if ($this->options->type->is('include')) {
                return $isFeatured;
            }

            return !$isFeatured;
        })->asArray());
    }
}