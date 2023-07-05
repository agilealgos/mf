<?php

namespace CouponsPlus\App\Conditions\BuiltIn\Filters;

use CouponsPlus\App\Conditions\CartItem;
use CouponsPlus\App\Conditions\Filter;
use CouponsPlus\App\Conditions\IntersectionFilter;
use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\App\Validators\InclusionValueValidator;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\Mapper\Types;

Class InTags extends Filter
{
    const TYPE = 'InTags';
    const SET = IntersectionFilter::class;
    
    public static function getName() : string
    {
        return __('In Tags', 'coupons-plus-international');
    }

    public static function getDescription() : string
    {
        return '';
    }

    public static function getOptions() : Collection
    {
        return InclusionValueValidator::getOptions();
    }

    public function filterSet(ItemsSet $itemsSet) : ItemsSet
    {
        (object) $inclusionValueValidator = new InclusionValueValidator($this->options->asArray());

        return new ItemsSet($itemsSet->getItems()->filter(function(CartItem $cartItem) use ($inclusionValueValidator) : bool {
           (array) $categoryIds = [];

            if ($cartItem->getProduct()->get_type() === 'variation') {
                $categoryIds = wc_get_product($cartItem->getProduct()->get_parent_id($context = 'edit'))->get_tag_ids($context = 'edit');
            } else {
                $categoryIds = $cartItem->getProduct()->get_tag_ids($context = 'edit');
            }

            $inclusionValueValidator->setValueToValidate(
                $categoryIds
            );

            return $inclusionValueValidator->isValid();
        })->asArray());
    }
}