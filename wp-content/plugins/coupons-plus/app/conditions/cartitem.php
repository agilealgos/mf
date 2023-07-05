<?php

namespace CouponsPlus\App\Conditions;

use CouponsPlus\App\Conditions\VirtualCartItem;
use CouponsPlus\Original\Abilities\Comparable;
use CouponsPlus\Original\Characters\StringManager;
use CouponsPlus\Original\Collections\Collection;
use \WC_Product;

Abstract Class CartItem implements Comparable
{
    private $state;
    protected $product;
    /**
     * This is not the same as the 'key'
     * This is used to identify a particualr cart item instance.
     */
    protected $uID;

    abstract protected function getStateCollection() : Collection;

    public function __construct(array $cartItem)
    {
        $this->state = new Collection($cartItem);
        $this->throwExceptionIfStateAintValid();
        $this->product = $this->getOriginalState()->get('data');
        $this->uID = spl_object_hash($this).random_int(10000, 10000000);
    }

    /**
     * NOT the same as the item's key
     * This is just to identify item instances.
     */
    public function getuID() : string
    {
        return $this->uID;   
    }
    

    public function getProduct() : WC_Product
    {
        return $this->product;
    }

    public function extract(int $quantity) : VirtualCartItem
    {
        (object) $virtualCartItem = new VirtualCartItem($this->getStateCollection()->asArray());

        $virtualCartItem->setState('quantity', $quantity);

        return $virtualCartItem;
    }

    /**
     * Splits evenly by quanitity.
     * For example, if the quantity of this cart item is 3, 
     * 3 virtual items will be returned.
     */
    public function split() : ItemsSet
    {
        (boolean) $cantBeSplit = $this->getState('quantity') === 1;

        if ($cantBeSplit) {
            return new ItemsSet([$this]);
        }

        (object) $newItems = new ItemsSet([]);

        (integer) $initialQuantity = $this->getState('quantity');
        (integer) $numberOfVirtualItems = 0;

        while ($numberOfVirtualItems < $initialQuantity) {
            $newItems->addItem($this->extract(1));
            $numberOfVirtualItems++;
        }

        return $newItems;
    }
    
    /**
     | Equivalent to the individual cart item array returned in WC()->cart->get_cart():
     |   Example:
     |      $cart_item['product_id'];
     |      $cart_item['variation_id'];
     |
     |      $cart_item['quantity'];
     |      $cart_item['line_subtotal']; 
     |      $cart_item['line_subtotal_tax'];
     */
    final public function getState(string $key = '') /* : Collection|string|null*/
    {
        if (!$key) {
            return $this->getStateCollection();
        }

        return $this->getStateValue($key);
    }

    final public function setState(string $key, $value) : CartItem
    {
        (string) $settterMethodName = "set{$key}";

        if (method_exists($this, $settterMethodName)) {
            $this->{$settterMethodName}($value, $key);
        } else {
            $this->getOriginalState()->set($key, $value);
        }

        return $this;
    }

    public function isTheSameAs(/*CartItem*/ $cartItem) : bool
    {
        if (!($cartItem instanceof CartItem)) {
            return false;
        }
        /* 
            Basically, the most important things here are:
            the key, quantity, and product id
            With THE KEY being the absolutely most imortant thing
        */
        return $this->getState()
                    ->except(['data'])
                    ->set('productId', $this->getProduct()->get_id($context = 'edit'))
                    ->asArray() == $cartItem->getState()
                                            ->except(['data'])
                                            ->set('productId', $cartItem->getProduct()
                                                                        ->get_id(
                                                                            $context = 'edit')
                                                                        )
                                            ->asArray();   
    }
    

    /**
     * If the child class has an accessor, it'll be used, otherwise
     * the raw value will be used instead
     */
    protected function getStateValue($key)
    {
        (string) $accessorMethodName = "get{$key}";

        if (method_exists($this, $accessorMethodName)) {
            return $this->{$accessorMethodName}();
        }

        return $this->getStateCollection()->get($key);
    }

    protected function getOriginalState() : Collection
    {
        return $this->state;   
    }
    
    /**
     * This is used across the plugin to compare two instances
     * It's hard to know where it's used since this is automatically triggered
     * when the object is in a string context. (eg: $cartItem1 == $cartItem2 | array_unique($cartItems), and more)
     * DO NOT REMOVE!
     */
    final public function __toString() : string
    {
        return $this->getState('key') ? "{$this->getState('key')}{$this->getState('quantity')}{$this->getState('line_subtotal')}" : '';
    }

    protected function throwExceptionIfStateAintValid()
    {
        //var_dump('chekin');//exit('e');
        //var_dump($this->state->get('data'));
        //var_dump(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10));
        if (!($this->state->get('data') instanceof WC_Product)) {
            throw new \InvalidArgumentException('CartItem requires a valid WC_Product via $cartItemData[\'data\']');
        }
    }
}