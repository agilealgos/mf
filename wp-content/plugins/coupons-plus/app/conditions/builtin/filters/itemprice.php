<?php

namespace CouponsPlus\App\Conditions\BuiltIn\Filters;

use CouponsPlus\App\Conditions\CartItem;
use CouponsPlus\App\Conditions\Filter;
use CouponsPlus\App\Conditions\IntersectionFilter;
use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\App\Quantities\AmountValidator;
use CouponsPlus\Original\Collections\Collection;

Class ItemPrice extends Filter
{
    const TYPE = 'ItemPrice';
    const SET = IntersectionFilter::class;
    
    public static function getName() : string
    {
        return __('Individual Item Price', 'coupons-plus-international');
    }

    public static function getDescription() : string
    {
        return '';
    }

    public static function getOptions() : Collection
    {
        return AmountValidator::getOptions();
    }

    public function filterSet(ItemsSet $itemsSet) : ItemsSet
    {
        (object) $amountValidator = new AmountValidator($this->options->asArray());

        return new ItemsSet($itemsSet->getItems()->filter(function(CartItem $cartItem) use ($amountValidator) : bool {
            $amountValidator->setAmount(
                $cartItem->getProduct()->get_price($context = 'edit')
            );

            return $amountValidator->isValid();
        })->asArray());
    }
}