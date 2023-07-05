<?php

namespace CouponsPlus\App\Conditions\Set;

use CouponsPlus\App\Quantities\AmountValidator;
use CouponsPlus\Original\Collections\Collection;
use Generator;
/**
 * SOME GOTCHAS:
 * DUPLICATES ARE IGNORED
 * EXAMPLE:
 *     FOR SET 
 *         (A)10,(B)25,(C)10
 *     IF WE WANT 
 *         = 35
 *    THIS WILL ONLY YIELD
 *       (A)10 + (B)25 (ONCE)
 *    USUALLY A SUBSET SUM SHOULD YIELD 
 *       (A)10 + (B)25
 *       (C)10 + (B)25
 *    BUT WE DON'T REALLY NEED IT FOR OUR CURRENT USE CASE
 *    AND THE LESS COMBINATIONS THE MORE PERFORMANCE
 *    
 */
Class SubsetSum
{
    const MODE_EQUALS = 100;
    const MODE_MINIMUM = 200;
    const MODE_MAXIMUM = 300;

    protected $numbers = [];
    protected $originalNumbers = [];
    protected $sum;
    protected $amountValidator;

    public function __construct(array $numbers)
    {
        $this->originalNumbers = $numbers;
        $this->numbers = $this->originalNumbers;   
        // Convert to Integers
        $this->removeNegatives();
        $this->convertFloatsToIntegers();
        $this->setdefaultAmountValidator();
    }

    public function setSetLengthValidator(AmountValidator $amountValidator)
    {
        $this->amountValidator = $amountValidator;   
    }

    public function equals(float $sum) : Collection
    {
        return $this->calculate($sum, static::MODE_EQUALS);
    }
    
    public function minimum(float $sum) : Collection
    {
        return $this->calculate($sum, static::MODE_MINIMUM);
    }

    public function maximum(float $sum) : Collection
    {
        return $this->calculate($sum, static::MODE_MAXIMUM);
    }

    protected function calculate(float $sum, int $mode) : Collection
    {
        $this->mode = $mode;

        (integer) $sum = $this->convertFloatToInteger($sum);

        if (!$this->passesEarlyOptimizations($sum)) {
            return new Collection([]);
        }

        // Sort from High to Low
        $this->mode === static::MODE_MAXIMUM? \asort($this->numbers) : \arsort($this->numbers);
        $keysMap = [
            /**
             * The original keys that map to their 
             * indexed counterpart
             */
            # 'index' => '(original) key'
        ];
        $indexedNumbers = [
            /**
             * This is the numbers as an indexed array
             * which the algorithm uses.
             */
        ];
        $duplicates = [
            /**
             * Only the keys of the duplicated numbers will be stored!
             * If you have 25x3, only two of them will be stored,
             * the other one will be used by the algorithm
             */
            # '(int) duplicatedNumber' => [key1, key2, ...]
        ];

        $entireSetHasDuplicateNumbers = \count(\array_unique($this->numbers)) === 1;
        /**
         * Initialization.
         *
         * Doesn't really matter how heavy the code is in this loop
         * since it only runs once.
         */
         $index = 0;
        foreach ($this->numbers as $key => $number) {
            /**
             * Convert array to indexed.
             * @var integer
             */
            $indexedNumbers[$index] = $number;
            $keysMap[$index] = $key;

            $index++;
        }
        $indexedNumbersLowToHigh = $indexedNumbers;
        $this->mode === static::MODE_MAXIMUM? \arsort($indexedNumbersLowToHigh) : \asort($indexedNumbersLowToHigh);
        /**
         * store duplicates
         */
        foreach ($this->numbers as $key => $number) {
            $duplicateNumbers = \array_filter($this->numbers, function($numberToCheck) use ($number) {
                return $numberToCheck === $number;
            });
            if (\count($duplicateNumbers) > 1) {
                if (!isset($duplicates[$number])) {
                    $offset = 0;
                    $duplicates[$number] = \array_keys($duplicateNumbers);
                }
            }
        }

        $solutions = array();
        $pos = array(0 => \count($indexedNumbers) - 1);
        $lastPosIndex = 0;
        $currentPos = $pos[0];
        $currentSum = 0;

        /**
         * TRY EARLY RETURNING WHEN POSSIBLE
         */
        (object) $earlySets = $this->tryCalculatingEarly($sum);
        if ($earlySets->haveAny()) {
            return $earlySets;
        }
        while (true) {
            $currentSum += $indexedNumbers[$currentPos];
            $setLength = $lastPosIndex + 1;

            if (($this->mode === static::MODE_MAXIMUM) && $currentSum <= $sum && $currentPos != 0) {
                // if its minium, try matching exaclty with the remaining items
                // otherwise just sum ony by one from the cheapest to the most expensive until you hit the maximum threshold

                $items = \array_slice($pos, 0, $setLength);

                if ($this->amountValidator->expects('minimum')) {
                    $set = $this->fillWithAsManyItemsAsPossibleBeforeHittingTheThreshold($sum, $currentSum, $indexedNumbersLowToHigh, $items, $setLength, $keysMap, $indexedNumbers);
                    // fill it with as many items as possible
                    // 
                }

                $this->amountValidator->setAmount(count($set));

                if ($this->amountValidator->isValid()) {
                    return $this->createSetFromOriginalArray($set, $keysMap, $indexedNumbers);
                }

                return new Collection([]);
                // then check here if the validayor is valid
                // then return that or an emty collection.


                //$pos[++$lastPosIndex] = --$currentPos;
                //return $this->createSetFromOriginalArray($items, $keysMap, $indexedNumbers);
            } elseif (($this->mode !== static::MODE_MAXIMUM) && $currentSum < $sum && $currentPos != 0) {
                $pos[++$lastPosIndex] = --$currentPos;
            } else {
                switch ($this->mode) {
                    case static::MODE_EQUALS:
                        $itsAMatch = $currentSum == $sum;
                        break;
                    case static::MODE_MINIMUM:
                        $itsAMatch = $currentSum >= $sum;
                        break;
                    case static::MODE_MAXIMUM:
                        $itsAMatch = $currentSum <= $sum;
                        break;
                }

                if ($itsAMatch) {
                    //$solution = [];
                    $items = \array_slice($pos, 0, $setLength);

                    $this->amountValidator->setAmount($setLength);
                    $lengthIsLooseyValid = $this->amountValidator->isValid();
                    $lengthIsValid = $lengthIsLooseyValid;

                    if ((($lengthIsLooseyValid && $this->amountValidator->expects('maximum')) || !$lengthIsLooseyValid && $this->amountValidator->expects('minimum')) && $this->lengthOfSetIsSmallerThanExpectedLength($setLength)) {

                        // try filling the array with as many items as possible before we hit the threshold
                        if ($set = $this->getSetWithRemainingNumbers($indexedNumbersLowToHigh, $items, $setLength, $keysMap, $indexedNumbers)) {

                            return $set;
                        }
                    } elseif ($lengthIsValid) {
                        return $this->createSetFromOriginalArray($items, $keysMap, $indexedNumbers);
                    } elseif ($this->mode === static::MODE_MINIMUM) {
                        if ($this->amountValidator->expects('equals')) {
                            // so a valid match has been found
                            // BUT the exact lenght of the set is not valid
                            // we expect equals but apparently the set is not the required length
                            // if less than tha required, try adding the remaining items, cheapest first. Remeber to avoid adding the items we already have
                            //var_dump('$setLength', $setLength, '$this->amountValidator->getExpectedAmount()', $this->amountValidator->getExpectedAmount(), $setLength < $this->amountValidator->getExpectedAmount());
                            //cexit('chekin');
                            if ($this->lengthOfSetIsSmallerThanExpectedLength($setLength)) {
                                //exit('is it true');
                                if ($set = $this->getSetWithRemainingNumbers(
                                    $indexedNumbersLowToHigh, $items, $setLength, $keysMap, $indexedNumbers)
                                ) {
                                    return $set;
                                }
                            }
                        }
                    }
                }

                if ($lastPosIndex == 0) {
                    break;
                }

                $currentSum -= $indexedNumbers[$currentPos] + $indexedNumbers[1 + $currentPos = --$pos[--$lastPosIndex]];
            }
        }

        return new Collection([]);
    }

    protected function lengthOfSetIsSmallerThanExpectedLength(int $setLength) : bool
    {
        return $setLength < $this->amountValidator->getExpectedAmount();   
    }

    protected function getSetWithRemainingNumbers(array $indexedNumbersLowToHigh, array $items, int $setLength, array $keysMap, array $indexedNumbers)  /* : Collection|null*/
    {
        $remainingNumbers = $this->getRemainingNumbers($indexedNumbersLowToHigh, $items);

        switch (true) {
            case $this->amountValidator->expects('equals'):
            case $this->amountValidator->expects('minimum'):
                $weCanAddMoreitems = $setLength + count($remainingNumbers) >= $this->amountValidator->getExpectedAmount();
                break;
            case $this->amountValidator->expects('maximum'):
                $weCanAddMoreitems = true;
                break;
            default:
                $weCanAddMoreitems = false;
                break;
        }
        if ($weCanAddMoreitems) {

            // here we just need ta add the remainng items to the exact amount!
            //var_dump('$items', $items);
            //var_dump('$remainingNumbers', $remainingNumbers);
            $itemsToAdd = array_slice(
                $remainingNumbers, 
                $offset = 0, 
                $length = $this->amountValidator->getExpectedAmount() - $setLength,
                $preserve_keys = true
            );
            //var_dump('itemstoadd', $itemsToAdd);
            $finalItemKeys = array_merge($items, array_keys($itemsToAdd));
            //var_dump('$finalItemKeys', $finalItemKeys);

            return $this->createSetFromOriginalArray(
                $finalItemKeys, $keysMap, $indexedNumbers
            );
        }   
    }
    
    protected function getRemainingNumbers(array $indexedNumbersLowToHigh, array $items) : array
    {
        return array_filter($indexedNumbersLowToHigh, function($index) use ($items) {
            return !in_array($index, $items);
        }, ARRAY_FILTER_USE_KEY);;   
    }
    
    protected function fillWithAsManyItemsAsPossibleBeforeHittingTheThreshold(int $targetSum, int $currentSum, array $indexedNumbersLowToHigh, array $items, int $setLength, array $keysMap, array $indexedNumbers) : array
    {
        $remainingNumbers = $this->getRemainingNumbers($indexedNumbersLowToHigh, $items);

        foreach ($remainingNumbers as $index => $remainingNumber) {

            if ($currentSum + $remainingNumber <= $targetSum) {
                $items[] = $index;
                $currentSum += $remainingNumber;
            }
        }

        return $items;
    }
    
    protected function tryCalculatingEarly(int $sum) : Collection
    {
        if ($this->mode === static::MODE_MINIMUM) {
            // if we expect a minimum, the sum of items meet the sum 
            // and the set length is higher then the minium
            // return all items
            $lengthPasses = count($this->numbers) >= $this->amountValidator->getExpectedAmount() && $this->amountValidator->expects('minimum');
            $sumPasses = array_sum($this->numbers) >= $sum;
            if ($lengthPasses && $sumPasses) {
                //exit('passing');
                //var_dump('count($this->numbers)', count($this->numbers), ' $this->amountValidator->getExpectedAmount()', $this->amountValidator->getExpectedAmount());

                //var_dump('array_sum($this->numbers)', array_sum($this->numbers), '$sum', $sum);
                //var_dump('_______________');

                return new Collection($this->originalNumbers);
            }
        }

        return new Collection([]);
    }
    
    protected function createSetFromOriginalArray(array $items, array $keysMap, $indexedNumbers) : Collection
    {
        $solution = [];

        foreach ($items as $indexOfMatchedNumber) {
            $solution[$keysMap[$indexOfMatchedNumber]] = $this->convertIntegerToFloat($indexedNumbers[$indexOfMatchedNumber]);
        }

        return new Collection($solution);   
    }
    
    protected function passesEarlyOptimizations(int $sum) : bool
    {
        $this->amountValidator->setAmount(count($this->numbers));

        switch ($this->mode) {
            case static::MODE_EQUALS:
                $sumofNumbersMeetThreshold = array_sum($this->numbers) >= $sum;
                break;
            case static::MODE_MINIMUM:
                $sumofNumbersMeetThreshold = array_sum($this->numbers) >= $sum;
                break;
            case static::MODE_MAXIMUM:
                $sumofNumbersMeetThreshold = array_sum($this->numbers) <= $sum || count(array_filter($this->numbers, function($number) use ($sum) : bool {
                    return $number <= $sum;
                }));
                break;
        }

        if ($this->amountValidator->expects('equals') || $this->amountValidator->expects('minimum')) {
            $numberOfItemsMeetThreshold = count($this->numbers) >= $this->amountValidator->getExpectedAmount();
        } elseif ($this->amountValidator->expects('maximum')) {
            // we'll deal with this later
            // since any combination can yield possible results.
            $numberOfItemsMeetThreshold = true;
        }

        if (!$sumofNumbersMeetThreshold || !$numberOfItemsMeetThreshold) {
            return false;
        }

        return true;
    }
    
    protected function removeNegatives()
    {
        $this->numbers = array_filter($this->numbers, function($number) {
            return $number > 0;
        });   
    }
    
    /**
     * Two decimal floats only
     */
    protected function convertFloatToInteger(float $number) : int
    {
        return \round($number, $precision = 2, PHP_ROUND_HALF_UP) * 100;   
    }

    protected function convertIntegerToFloat(int $number) : float
    {
        return $number / 100;
    }

    protected function setdefaultAmountValidator()
    {
        $this->setSetLengthValidator(new AmountValidator([
            'quantity' => [
                'type' => 'minimum',
                'amount' => 1,
            ]
        ]));
    }

    protected function convertFloatsToIntegers()
    {
        foreach ($this->numbers as $key => $number) {
            $this->numbers[$key] = $this->convertFloatToInteger($number);
        }   
    }
}