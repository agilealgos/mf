<?php

namespace CouponsPlus\App\Conditions\BuiltIn\Conditions\Order\Filters;

use CouponsPlus\App\Validators\InclusionValueValidator;
use CouponsPlus\Original\Collections\Collection;
use WC_Order_Item_Product;

Class OrderItemsCategoriesFilter extends OrderItemsFilter
{
    public static function getOptions() : Collection
    {
        /** 
         * only one of them needs to match for this to fail or pass.
         * expectedValues are the IDs
         * only 'allowed' is supported in this context,
         */
        return InclusionValueValidator::getOptions()->only(['expectedValues']);   
    }
    
    public function getFilteredItems() : Collection
    {
        (object) $inclusionValueValidator = new InclusionValueValidator([
            'expectedValues' => $this->options->expectedValues->asArray(),
            'inclusionType' => 'allowed'
        ]);

        return $this->items->filter(function(WC_Order_Item_Product $orderItemProduct) use ($inclusionValueValidator) {
            $inclusionValueValidator->setValueToValidate(
                $orderItemProduct->get_product()->get_category_ids($context = 'edit')
            );

            return $inclusionValueValidator->isValid();
        });       
    }
}