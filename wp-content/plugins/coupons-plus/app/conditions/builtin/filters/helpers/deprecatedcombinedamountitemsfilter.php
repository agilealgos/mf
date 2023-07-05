<?php

namespace CouponsPlus\App\Conditions\BuiltIn\Filters\Helpers;

use CouponsPlus\App\Conditions\CartItem;
use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\App\Quantities\AmountValidator;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\MappedObject;
use Mistralys\SubsetSum\SubsetSum;

Class DeprecatedCombinedAmountItemsFilter
{
    protected $amountValidator;
    protected $setToFilter;

    public static function getOptionsMap() : Collection
    {
        return AmountValidator::getOptions();  
    }

    public function __construct(ItemsSet $setToFilter, MappedObject $options, string $stateKey)
    {
        $this->setToFilter = $setToFilter;
        $this->options = $options;
        $this->stateKey = $stateKey;
    }

    public function getFilteredItems() : ItemsSet
    {
        (object) $itemsSet = $this->setToFilter;
        (object) $amountValidator = $this->getAmountValidator();

        switch ($amountValidator->gettype()) {
            case 'equals':
                return $this->getExactAmount($itemsSet, $this->getAmountValidator()->getExpectedAmount());
                break;
            case 'minimum':
                return $this->getMinimum($itemsSet);
                break;
            case 'maximum':
                return $this->getMaximum($itemsSet);
                break;
            case 'range':
                return $this->getRange($itemsSet);
                break;
        }
    }

    protected function getExactAmount(ItemsSet $itemsSet, float $sumAmount, bool $keepZeros = false) : ItemsSet
    {
        (object) $set = $itemsSet->getItems()->map(function(CartItem $cartItem) {
            return $cartItem->getState($this->stateKey);
        });

        if ($sumAmount === ((float) 0)) {
            //SubsetSum doesn't seem to handle zeros (it was almost too good to be true!)
            (array) $matchedQuantities = $set->filter(function(float $amount) : bool {
                return $amount === ((float) 0);
            })->asArray();
        } else {
            (object) $subset = SubsetSum::create(
                $sumAmount,
                $set->asArray()
            );

            (array) $matchedQuantities = $subset->hasMatches() ? $subset->getMatches()[0] : [];
        }

        (boolean) $hasMatches = (boolean) count($matchedQuantities);

        return new ItemsSet($itemsSet->getItems()->filter(function(CartItem $cartItem) use (&$matchedQuantities, $keepZeros, $hasMatches) {
            (float) $valueToSearch = (float) $cartItem->getState($this->stateKey);

            if ($keepZeros && $hasMatches && $valueToSearch < 0.1) {
                return true;
            }
            /*int|false*/ $key = array_search($valueToSearch, $matchedQuantities);
            if ($key !== false) {
                // we need to remove it since we might have products with the same quantities.
                // eg: we want = 1 in total and there are two cartItems with 1 qty.
                // for more info please refer to NumberOfItemsTest::test_type_is_equals
                // (not included in production builds, sorry!)
                unset($matchedQuantities[$key]);
                return true;
            }
        })->asArray());
    }

    protected function getMinimum(ItemsSet $itemsSet) : ItemsSet
    {
        return $this->getOriginalSetIfValid($itemsSet);
    }

    /**
     * Used in more than one context, if modified, make sure it works in all places
     *
     */
    protected function getOriginalSetIfValid(ItemsSet $itemsSet) : ItemsSet
    {
        $this->getAmountValidator()->setAmount($itemsSet->getCombinedField($this->stateKey));

        if ($this->getAmountValidator()->isValid()) {
            if ($this->getAmountValidator()->getExpectedAmount() > 0) {
                return new ItemsSet($itemsSet->getPositiveItems($this->stateKey)->asArray());
            }
            return $itemsSet;
        }

        return new ItemsSet([]);
    }
    

    protected function getMaximum(ItemsSet $itemsSet, AmountValidator $amountValidator = null) : ItemsSet
    {
        $amountValidator = $amountValidator ?? $this->getAmountValidator();
        $amountValidator->setAmount($itemsSet->getCombinedField($this->stateKey, $positiveOnly = false));

        if ($amountValidator->isValid()) {
            return $itemsSet;
        }

        //$this->getAmountValidator() can be different from the $amountValidator passed
        //do not remove this calls
        (float) $minimum = $this->getAmountValidator()->getExpectedRangeAmount('minimum')?: 0; 
        (float) $maximum = $this->getAmountValidator()->getExpectedRangeAmount('maxmimum')?: $amountValidator->getExpectedAmount();
        (boolean) $minimumIsWhole = floor($minimum) == $minimum;
        (boolean) $maximumIsWhole = floor($maximum) == $maximum;

        (float) $step = !$minimumIsWhole || !$maximumIsWhole || $minimum == 0? 0.01 : 1; # if we're dealing with floats or integers

        (array) $possibleSums = array_reverse(range(
            $minimum,
            $maximum,
            $step
        ));

       foreach ($possibleSums as $possibleSum) {
            (object) $possibleItemsSet = $this->getExactAmount($itemsSet, $possibleSum, $keepZeros = true);

            if ($possibleItemsSet->getItems()->haveAny()) {
                return $possibleItemsSet;
            }
       }

       return new ItemsSet([]);
    }

    protected function getRange(ItemsSet $itemsSet) : ItemsSet
    {
        (object) $minimumValidator = new AmountValidator([
            'quantity' => [
                'type' => 'minimum',
                'amount' => $this->getAmountValidator()->getExpectedRangeAmount('minimum'),
            ]
        ]);
        $minimumValidator->setAmount($itemsSet->getCombinedField($this->stateKey));

        // if we don't even have the minimum, there's nothin we can do
        if (!$minimumValidator->isValid()) {
            return new ItemsSet([]);
        }

        //but we do have a minimum!, so let's try getting as much items as possible before we pass the maximum
        return $this->getMaximum($itemsSet, new AmountValidator([
            'quantity' => [
                'type' => 'maximum',
                'amount' => $this->getAmountValidator()->getExpectedRangeAmount('maxmimum'),
            ]
        ]));
    }
    
    protected function getAmountValidator() : AmountValidator
    {
        if (!($this->amountValidator instanceof AmountValidator)) {
            $this->amountValidator = new AmountValidator($this->options->asArray());
        }

        return $this->amountValidator;
    }
}