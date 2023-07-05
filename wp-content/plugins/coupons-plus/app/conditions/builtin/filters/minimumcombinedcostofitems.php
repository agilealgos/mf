<?php

namespace CouponsPlus\App\Conditions\BuiltIn\Filters;

use CouponsPlus\App\Conditions\Filter;
use CouponsPlus\App\Conditions\IntersectionFilter;
use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\App\Quantities\AmountValidator;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\Mapper\Types;

Class MinimumCombinedCostOfItems extends Filter
{
    const TYPE = 'MinimumCombinedCostOfItems';
    const SET = IntersectionFilter::class;
    
    public static function getName() : string
    {
        return __('Minimum Combined Cost Of Items', 'coupons-plus-international');
    }

    public static function getDescription() : string
    {
        return '';
    }

    public static function getOptions() : Collection
    {
        return new Collection([
            'amount' => Types::FLOAT()->meta([
                'name' => __('Minimum', 'coupons-plus-international')
            ])
        ]);
    }

    public function filterSet(ItemsSet $itemsSet) : ItemsSet
    {
        (object) $amountValidator = new AmountValidator([
            'quantity' => [
                'type' => 'minimum',
                'amount' => $this->options->amount,
            ]
        ]);

        $amountValidator->setAmount($itemsSet->getTotalCost());

        if ($amountValidator->isValid()) {
            (array) $items = $itemsSet->getOrderedByCheapestProduct()->asArray();
        } else {
            (array) $items = [];
        }

        return new ItemsSet($items);
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