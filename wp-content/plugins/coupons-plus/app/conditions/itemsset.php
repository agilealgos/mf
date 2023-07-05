<?php

namespace CouponsPlus\App\Conditions;

use CouponsPlus\App\Conditions\CartItem;
use CouponsPlus\App\Conditions\RealCartItem;
use CouponsPlus\App\Conditions\VirtualCartItem;
use CouponsPlus\App\Coupon\CartComponentsSet;
use CouponsPlus\App\Offers\BuiltIn\ExtraProduct;
use CouponsPlus\Original\Abilities\Comparable;
use CouponsPlus\Original\Collections\Collection;

Class ItemsSet extends CartComponentsSet implements Comparable
{
    public static function createFromAllCartItems() : ItemsSet
    {
        return new static(WC()->cart->get_cart());
    }

    public function __construct(...$arguments)
    {
        //add_action('woocommerce_cart_item_removed', [$this, 'updateSet']);
        parent::__construct(...$arguments);
    }
    
    public function __destruct()
    {
        remove_action('woocommerce_cart_item_removed', [$this, 'updateSet']);
    }
    
    public function getInitialItemsCollection(array $items) : Collection
    {
        return (new Collection($items))->map(function(/*array|CartItem*/$cartItem) {
            return $cartItem instanceof CartItem? $cartItem : new RealCartItem($cartItem);
        });   
    }
    
    /**
     * Gets all ***PUBLICLY*** available items
     * Private items (such as those added by ExtraProduct) are ***NOT*** RETURNED!
     */
    public function getItems() : Collection
    {
        return $this->items->filter(function(CartItem $cartItem) : bool {
            // only those added by the user and NOT by us.
            return !$cartItem->getState(ExtraProduct::PRODUCT_ADDED_WITH_DISCOUNT_TYPE);
        });
    }

    public function getUnfilteredItems() : Collection
    {
        return $this->items;   
    }

    public function withVirtualItemsRolledBackIfPossible() : ItemsSet
    {
        if ($this->hasVirtualItems()) {
            (object) $virtualItemsGroupedBySameCartItem = $this->groupVirtualItemsByCartType();
            (object) $newItemsSet = new static($this->items->asArray());
            foreach ($virtualItemsGroupedBySameCartItem->asArray() as $virtualCartItems) {
                (object) $virtualItemsSet = new static($virtualCartItems->asArray());
                (object) $RealCartItem = $virtualCartItems->first()
                                                              ->getRealCartItem();
                (boolean) $virtualItemsOfThistypeHaveTheSameQuantityAsTheRealItem = $virtualItemsSet->getTotalQuantity() === $RealCartItem->getState('quantity');

                if ($virtualItemsOfThistypeHaveTheSameQuantityAsTheRealItem) {
                    // items can be rolled back to a Real cart item
                    // so we need to:
                    // 1 remove the virtaul items
                    // 2 and replace em with the single real item
                    (object) $shouldCartItemMatchRealItem = function(bool $shouldMatch) use ($RealCartItem) : callable {
                        return function(CartItem $cartItem) use ($shouldMatch, $RealCartItem) : bool {
                            // basically, we want to remove all items with the key of the virtual items, then we'll add the real item back later
                            (boolean) $iscartItemSameAsReal = $cartItem->getState('key') === $RealCartItem->getState('key');
                            return $shouldMatch? $iscartItemSameAsReal : !$iscartItemSameAsReal; 
                        };
                    };

                    (string) $indexOfTheFirstVirtualItem = $newItemsSet->items->findKey(
                        $shouldCartItemMatchRealItem(true)
                    );
                    // keys should be preserved here at this point because we are 
                    // replacing the first item using its key
                    // in the expresion bellow this one
                    // do not add a Collection::getValues() call **yet**
                    $newItemsSet->items = $newItemsSet->items->filter(
                        $shouldCartItemMatchRealItem(false)
                    );
                    // here's were we set it using the old key
                    $newItemsSet->items->set(
                        $indexOfTheFirstVirtualItem, 
                        $RealCartItem
                    );
                }
                $newItemsSet->items = $newItemsSet->items->sortByKey();
                $newItemsSet->items = $newItemsSet->items->getValues();
            }

            return $newItemsSet->items->asArray() !== $this->items->asArray()? $newItemsSet : $this;
        }

        return $this;
    }

    public function hasVirtualItems() : bool
    {
        return (boolean) $this->items->find(function(CartItem $cartItem) : bool {
            return $cartItem instanceof VirtualCartItem;
        });
    }
    
    /**
     * Ordered by the price of a single product,
     * NOT the line item total (NOT the price x amount of items)
     * just the individual product price
     */
    public function getOrderedByCheapestProduct() : Collection
    {
        return $this->getItems()->sort(function(CartItem $cartItemOne, CartItem $cartItemTwo) : int {
            (float) $firstProductPrice = (float) $cartItemOne->getProduct()->get_price($context = 'edit');
            (float) $secondProductPrice = (float) $cartItemTwo->getProduct()->get_price($context = 'edit');

            return $firstProductPrice <=> $secondProductPrice;
        });   
    }

    public function addItem(Cartitem $cartItem)
    {
        $this->items->push($cartItem);   
    }
    
    public function addItems(/*array|Collection|ItemsSet*/ $items)
    {
        (object) $items = $items instanceof ItemsSet? $items->getItems() : new Collection($items);

        $this->items = $this->getItems()->append($items)->filter(function($item) : bool {
            return $item instanceof CartItem;
        })->withoutDuplicates();
    }

    public function setItems(/*array|Collection|ItemsSet*/ $items)
    {
        $this->items = $items instanceof ItemsSet? $items->getItems() : new Collection($items);
    }

    public function splitItems() : ItemsSet
    {
        (object) $individualItemsSet = new ItemsSet([]);

        foreach ($this->items->asArray() as $cartItem) {
            foreach ($cartItem->split()->getItems()->asArray() as $individualCartItem) {
                $individualItemsSet->addItem($individualCartItem);
            }
        }

        return $individualItemsSet;
    }
    
    public function isTheSameAs(/*ItemsSet*/ $itemsSetToCheck) : bool
    {
        if (!($itemsSetToCheck instanceof static)) {
            return false;
        }
        /**
         * To know:
         * Collection::areTheSameAs() uses Collection::has()
         * which uses Comparable::isTheSameAs() on Comparable objects
         * CartItem is a Comparable object, 
         * so $cartItem->isTheSameAs($otherCartItem) is being called under the hood.
         *
         * Pretty cool, huh
         * 
         */
        return $this->withVirtualItemsRolledBackIfPossible()
                    ->getItems()
                    ->areTheSameAs(
                        $itemsSetToCheck->withVirtualItemsRolledBackIfPossible()
                                        ->getItems()
                    );

                    /*
        return $this->items->getValues()->reduce(
            function(bool $lastCartItemIsTheSame, CartItem $cartItem, int $key) use ($itemsSetToCheck) : bool {

                // if only one of em is not the same, skip checking
                // because these sets are sure as h not the same
                if (!$lastCartItemIsTheSame) {
                    return false;
                }

                return $itemsSetToCheck->getItems()->has($cartItem);
            },
            $initial = $this->items->haveAny()? $itemsSetToCheck->getItems()->haveAny() : $itemsSetToCheck->getItems()->haveNone()
        );*/
    }
    
    public function getCombined(callable $callback, $initial = 0, bool $positiveOnly = true) : float
    {
        return $this->getItems()->reduce($callback, $initial = 0);
    }

    public function getCombinedField(string $field, bool $positiveOnly = true) : float
    {
        return $this->getCombined(function(float $count, CartItem $cartItem) use ($field) {
            if (!($cartItem->getState($field) > 0)) {
                return $count;
            }

            return $count + $cartItem->getState()->get($field);
        }, $initial = 0, $positiveOnly);
    }
    
    public function filterSum(array $options) : ItemsSet
    {
        /**
         * $this->filterSum([
         *     'numberOfItems' => [
         *         'equals' => 5
         *     ],
         *     ''
         * ])
         */
    }
    
    public function getPositiveItems(string $field) : Collection
    {
        return $this->getItems()->filter(function(CartItem $cartItem) use ($field) : bool {
            return $cartItem->getState($field) > 0;
        });
    }
    
    public function getTotalQuantity() : int
    {
        return $this->getCombinedField('quantity');
    }

    public function getTotalCost() : float
    {
        return $this->getCombined(function(float $count, CartItem $cartItem) {
            return $count + (
                    $cartItem->getProduct()->get_price($context = 'edit') * $cartItem->getState('quantity')
                   );
        }, $initial = 0);
    }

    public function updateSet(string $cart_item_key)
    {
        $this->items = $this->items->filter(function(CartItem $cartItem) use ($cart_item_key) : bool {
            return $cartItem->getState('key') !== $cart_item_key; 
        });
    }

    /**
     * Might return *LESS* that the maximum amount.
     */
    public function getAMaximumOf(int $amount) : ItemsSet
    {
        (object) $itemsToReturn = new static([]);

        foreach ($this->getOrderedByCheapestProduct()->asArray() as $cartItem) {
            if ($itemsToReturn->getTotalQuantity() == $amount) {
                break;
            }

            (integer) $numberOfItemsLeftToAdd = $amount - $itemsToReturn->getTotalQuantity();

            (boolean) $hasExactQuantity = $cartItem->getState('quantity') == $numberOfItemsLeftToAdd;
            (boolean) $hasLessThanRequired = $cartItem->getState('quantity') < $numberOfItemsLeftToAdd;

            if ($hasExactQuantity || $hasLessThanRequired) {
                $itemsToReturn->items->push($cartItem);
            } else {
                // non exact, needs extractin'
                (object) $virtualCartItem = $cartItem->extract($numberOfItemsLeftToAdd);

                $itemsToReturn->items->push($virtualCartItem);
            }
        }   

        return $itemsToReturn;
    }

    protected function groupVirtualItemsByCartType() : Collection
    {
        (object) $allVirtualItems = $this->getVirtualItems();
        (object) $grouppedVirtualItems = $allVirtualItems->mapWithKeys(function(CartItem $cartItem) : array {
            return [
                'key' => $cartItem->getState('key'),
                'value' => new Collection([])
            ];
        });

        foreach ($allVirtualItems->asArray() as $virtualItem) {
            $grouppedVirtualItems->get($virtualItem->getState('key'))
                                 ->push($virtualItem);
        }

        return $grouppedVirtualItems->filter();
    }

    protected function getVirtualItems() : Collection
    {
        return $this->items->filter(function(CartItem $cartItem) : bool {
            return $cartItem instanceof VirtualCartItem;
        });   
    }
    
    
}