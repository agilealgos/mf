<?php

namespace CouponsPlus\App\Conditions\BuiltIn\Filters;

use CouponsPlus\App\Conditions\CartItem;
use CouponsPlus\App\Conditions\Filter;
use CouponsPlus\App\Conditions\IntersectionFilter;
use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\App\Validators\InclusionValueValidator;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\Mapper\Types;

Class InCategories extends Filter
{
    const TYPE = 'InCategories';
    const SET = IntersectionFilter::class;
    
    public static function getName() : string
    {
        return __('In Categories', 'coupons-plus-international');
    }

    public static function getDescription() : string
    {
        return '';
    }

    public static function getOptions() : Collection
    {
        return InclusionValueValidator::getOptions(
            $expectedValues = [
                'meta' => [
                    'name' => __('Categories', 'coupons-plus-international')
                ]
            ]
        );
    }

    public function filterSet(ItemsSet $itemsSet) : ItemsSet
    {
        (object) $inclusionValueValidator = new InclusionValueValidator($this->options->asArray());

        return new ItemsSet($itemsSet->getItems()->filter(function(CartItem $cartItem) use ($inclusionValueValidator) : bool {

            (array) $categoryIds = [];

            if ($cartItem->getProduct()->get_type() === 'variation') {
                (object) $product = wc_get_product($cartItem->getProduct()->get_parent_id($context = 'edit'));
            } else {
                (object) $product = $cartItem->getProduct();
            }

            $inclusionValueValidator->setValueToValidate(
                $this->getCategoriesWithAncestors($product)->asArray()
            );

            return $inclusionValueValidator->isValid();
        })->asArray());
    }

    protected function getCategoriesWithAncestors(\WC_Product $product) : Collection
    {
        (array) $categoryIds = new Collection($product->get_category_ids($context = 'edit'));

        foreach ($categoryIds->asArray() as $categoryId) {
            $categoryIds->append(get_ancestors($categoryId, 'product_cat'));
        }   

        return $categoryIds;
    }
    
}