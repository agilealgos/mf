<?php

namespace CouponsPlus\App\Conditions\BuiltIn\Conditions\Order\Filters;

use CouponsPlus\App\Validators\InclusionValueValidator;
use CouponsPlus\Original\Collections\Collection;
use WC_Order_Item_Product;

Class OrderItemsTagsFilter extends OrderItemsFilter
{
    public static function getOptions() : Collection
    {
        /** 
         * only one of them needs to match for this to fail or pass.
         * For example, if exected ids are 50 and 70:
         * the product must have *either* 50 or 70, NOT 50 and 70,
         * if the product has IDs: 40, 50 and 60 it will pass because it has 50 even though
         * it doesn't have 70. We might add an option to change this behaviour
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
                $orderItemProduct->get_product()->get_tag_ids($context = 'edit')
            );

            return $inclusionValueValidator->isValid();
        });       
    }
}