<?php

namespace CouponsPlus\App\Conditions\BuiltIn\Filters\Helpers;

use CouponsPlus\App\Conditions\CartItem;
use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\App\Quantities\AmountValidator;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\JSONMapper;
use CouponsPlus\Original\Collections\MappedObject;
use Mistralys\SubsetSum\SubsetSum;

Class CombinedAmountItemsFilter
{
    protected $amountValidator;
    protected $setToFilter;
    /**
     * SubsetSum mode not available on 'quantity'
     * Support may be added in the future
     * but rn it's not really needed so...
     */
    protected $modeIsSubsetSum;

    public static function getOptionsMap() : Collection
    {
        return AmountValidator::getOptions();  
    }

    public function __construct(ItemsSet $setToFilter, array $options, string $stateKey)
    {
        $this->setToFilter = $setToFilter;
        $this->options = (new JSONMapper(static::getOptionsMap()->asArray()))->smartMap($options);
        $this->stateKey = $stateKey;
    }

    public function getFilteredItems() : ItemsSet
    {
        $this->modeIsSubsetSum = false;

        return $this->filterItems();
    }

    public function getFilteredItemsSubsetSum() : Collection
    {
        // CURRENTLY NOT AVAILABLE ON 'QUANTITY' SATE KEY
        $this->modeIsSubsetSum = true;

        return $this->filterItems();
    }
    
    protected function filterItems() /*: ItemsSet|Collection*/
    {
        (object) $itemsSet = $this->setToFilter;
        (object) $amountValidator = $this->getAmountValidator();

        switch ($amountValidator->gettype()) {
            case 'equals':
                return $this->getExactAmount(
                    $itemsSet, 
                    $this->getAmountValidator()->getExpectedAmount()
                );
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
    
    protected function getExactAmount(ItemsSet $itemsSet, float $sumAmount, bool $keepZeros = false) /* : ItemsSet|Collection */
    {
        /**
         * This should be used when mode is NOT subsetsum BECAUSE OF the perfomance gains.
         * 'Quantity' has the potential to create hundreds and even thousands of combinations
         * with subsetsum so it's very important that we use this 
         * to prevent generating all the combinations when there not needed.
         */
        if (!$this->modeIsSubsetSum && $this->stateKey === 'quantity') {
            if ($itemsSet->getCombinedField($this->stateKey) === $sumAmount) {
                return $itemsSet;
            }

            (object) $itemsToReturn = $itemsSet->getAMaximumOf($sumAmount);

            return $itemsToReturn->getTotalQuantity() == $sumAmount? $itemsToReturn : new ItemsSet([]);
        }

        (object) $allItemsSplitIndividually = $itemsSet->splitItems()
                                                        ->getOrderedByCheapestProduct();
        (object) $allIndividualItemsMap = $allItemsSplitIndividually->mapWithKeys(
            function(CartItem $cartItem) : array{
                return [
                    'key' => $cartItem->getuID(),
                    'value' => $cartItem->getState($this->stateKey)
                ];
            }
        );                              

        if ($sumAmount === ((float) 0)) {
            //SubsetSum doesn't seem to handle zeros (it was almost too good to be true!)
            (array) $matchedQuantities = $allIndividualItemsMap->filter(function(float $amount) : bool {
                return $amount === ((float) 0);
            })->asArray();
            (array) $mapOfAllPossibleMatches = [$matchedQuantities];
        } else {
            (object) $subset = SubsetSum::create(
                $sumAmount,
                $allIndividualItemsMap->asArray()
            );

            (array) $mapOfAllPossibleMatches = $subset->hasMatches() ? $subset->getMatches() : [];
            (array) $matchedQuantities = $subset->hasMatches() ? $subset->getMatches()[0] : [];
        }

        (boolean) $hasMatches = (boolean) count($matchedQuantities);

        (array) $argumentsForFindingTheSets = [
            $allItemsSplitIndividually, 
            $mapOfAllPossibleMatches, 
            $hasMatches,
            $keepZeros
        ];
//var_dump('$this->modeIsSubsetSum', $this->modeIsSubsetSum, 'itemsset', $itemsSet->getItems()->map(function($cartiTme) {
  //  return "{$cartiTme->getProduct()->get_price('false')} x {$cartiTme->getState('quantity')}";
//})->asArray());
        return $this->modeIsSubsetSum? 
            $this->findAllItemSets(...$argumentsForFindingTheSets) : 
            $this->findTheCheapestSet(...$argumentsForFindingTheSets);
    }

    protected function findTheCheapestSet(Collection $allItemsSplitIndividually, array $mapOfAllPossibleMatches, bool $hasMatches, bool $keepZeros = false) : ItemsSet
    {
        /*object|null*/ $cheapestItemsSet = $this->findAllItemSets(...func_get_args())->first();

        return $cheapestItemsSet instanceof ItemsSet? $cheapestItemsSet : new ItemsSet([]);
    }
    
    protected function findAllItemSets(Collection $allItemsSplitIndividually, array $mapOfAllPossibleMatches, bool $hasMatches, bool $keepZeros = false) : Collection
    {
        (object) $setOfItemsSet = new Collection([]);
//var_dump('$mapOfAllPossibleMatches', $mapOfAllPossibleMatches);
        foreach ($mapOfAllPossibleMatches as $mapOfMatchedItems) {
            (object) $createdItemsSet = new ItemsSet(
                (new Collection($mapOfMatchedItems))->map(function(/*mixed*/ $value, string $uID) use ($allItemsSplitIndividually) : CartItem {
                    return $allItemsSplitIndividually->find(function(Cartitem $cartItem) use ($uID): bool {
                        return $cartItem->getuID() === $uID;
                    });
                })->asArray()/*
                $allItemsSplitIndividually->filter(function(CartItem $cartItem) use ($mapOfMatchedItems, $keepZeros, $hasMatches) {
                    (float) $cartItemValue = (float) $cartItem->getState($this->stateKey);

                    if ($keepZeros && $hasMatches && $cartItemValue < 0.1) {
                        return true;
                    }

                    return ;
                    
                    $key = array_search($cartItemValue, $matchedQuantities);
                    if ($key !== false) {
                        // we need to remove it since we might have products with the same quantities.
                        // eg: we want = 1 in total and there are two cartItems with 1 qty.
                        // for more info please refer to NumberOfItemsTest::test_type_is_equals
                        // (not included in production builds, sorry!)
                        unset($matchedQuantities[$key]);
                        return true;
                    }
                })->asArray()*/
            );
            /**
             * NOTE: we're currently not rolling back the items HERE since this 
               is a little expensive to do (not by itself but since we might have dozens of ItemsSets with potentially dozens of cart items each, it adds up real fast especially considering that most of these itemssets will most likely not get used)

               Equality should always be checked using ItemsSet::isTheSameAs()
             */
            $setOfItemsSet->push($createdItemsSet);
        }

        return $setOfItemsSet->sort(function(ItemsSet $setA, ItemsSet $setB) : int {
            return $setA->getTotalCost() <=> $setB->getTotalCost();
        });
    }
    
    protected function getMinimum(ItemsSet $itemsSet) /* : ItemsSet|Collection*/
    {
        if (!$this->modeIsSubsetSum) {
            return $this->getOriginalSetIfValid($itemsSet);
        }
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
                return new ItemsSet(
                    (new ItemsSet(
                        $itemsSet->getPositiveItems($this->stateKey)->asArray()
                    ))->getOrderedByCheapestProduct()->asArray()
                );
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
        //do not remove these calls
        (float) $minimum = $this->getAmountValidator()->getExpectedRangeAmount('minimum')?: 0; 
        (float) $maximum = $this->getAmountValidator()->getExpectedRangeAmount('maxmimum')?: $amountValidator->getExpectedAmount();
        (boolean) $minimumIsWhole = floor($minimum) == $minimum;
        (boolean) $maximumIsWhole = floor($maximum) == $maximum;

        (float) $step = !$minimumIsWhole || !$maximumIsWhole || $minimum == 0? 0.01 : 1; # if we're dealing with floats or integers

        if ($this->stateKey === 'quantity') {
            (object) $itemsToReturn = $itemsSet->getAMaximumOf($maximum);

            return $itemsToReturn;
        }

        //
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