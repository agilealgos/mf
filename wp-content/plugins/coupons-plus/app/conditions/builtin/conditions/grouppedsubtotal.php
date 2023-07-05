<?php

namespace CouponsPlus\App\Conditions\BuiltIn\Conditions;

use CouponsPlus\App\Conditions\BuiltIn\Conditions\Base\Subtotal;
use CouponsPlus\App\Conditions\BuiltIn\Filters\InCategories;
use CouponsPlus\App\Conditions\BuiltIn\Filters\InTags;
use CouponsPlus\App\Conditions\Condition;
use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\App\Quantities\AmountValidator;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\Mapper\Types;

Class GrouppedSubtotal extends Subtotal
{
    const TYPE = 'GrouppedSubtotal';
    
    static public function getName() : string
    {
        return __('Subtotal in Categories or Tags', 'coupons-plus-international');
    }

    static public function getDescription() : string
    {
        return '';
    }
    
    public static function getOptions() : Collection
    {
        return new Collection([
            'grouppedType' => Types::STRING()->withDefault('categories')
                                     ->allowed([
                                        __('Categories', 'coupons-plus-international') => 'categories',
                                        __('Tags', 'coupons-plus-international') => 'tags'
                                     ]),
            'ids' => Types::COLLECTION(),
            'amountOptions' => AmountValidator::getOptions()->asArray()
        ]);
    }
    
    protected function getAmountOptions() : array
    {          
            // poor man's STDClass to array
        return json_decode(
            json_encode($this->options->asArray()['amountOptions']), 
            $returnArray = true
        );
    }

    protected function getSubtotal() : float
    {
        (object) $items = $this->getFilteredItems();

        return $items->getTotalCost();
    }

    protected function getFilteredItems() : ItemsSet
    {
        switch ($this->options->grouppedType) {
            case 'categories':
                (object) $inCategories = new InCategories([
                    'inclusionType' => 'allowed',
                    'expectedValues' => $this->options->ids->asArray()
                ]);

                return $inCategories->filterSet(ItemsSet::createFromAllCartItems());
            break;
            case 'tags':
                (object) $inTags = new InTags([
                    'inclusionType' => 'allowed',
                    'expectedValues' => $this->options->ids->asArray()
                ]);

                return $inTags->filterSet(ItemsSet::createFromAllCartItems());
            break;
        }   
    }
}