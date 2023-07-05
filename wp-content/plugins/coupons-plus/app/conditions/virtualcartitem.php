<?php

namespace CouponsPlus\App\Conditions;

use CouponsPlus\App\Conditions\RealCartItem;
use CouponsPlus\Original\Collections\Collection;

Class VirtualCartItem extends CartItem
{
    private $quantity = 0;

    public function __construct(...$arguments)
    {
        parent::__construct(...$arguments);

        $this->setQuantity($this->getOriginalState()->get('quantity'));
    }

    public function getRealCartItem() : RealCartItem
    {
        return new RealCartItem($this->getOriginalState()->asArray());
    }
    
    protected function getStateCollection() : Collection
    {
        return $this->getOriginalState()->map(function($value, string $key) {
            switch ($key) {
                case 'quantity':
                    return $this->quantity;
                    break;
                case 'line_subtotal':
                    return $this->getSubtotal();
                    break;
                case 'line_subtotal_tax':
                    return $this->getTotalTax();
                    break;
                case 'line_total':
                    return $this->getTotalTax() + $this->getSubtotal();
                    break;           
                default:
                    return $value;
                    break;
            }
        });
    }

    protected function setQuantity(int $quantity)
    {
        $this->quantity = $quantity;
    }
    
    protected function getQuantity() : int
    {
        return $this->quantity;   
    }

    protected function getSubtotal() : float
    {
        return $this->quantity * $this->getProduct()->get_price($context = 'edit');   
    }
    
    protected function getTotalTax() : float
    {
        (float) $taxPerItem = ($this->getOriginalState()->get('line_subtotal_tax') / $this->getOriginalState()->get('quantity'));

        return $taxPerItem * $this->quantity;   
    }
}