<?php

namespace CouponsPlus\App\Quantities;

use CouponsPlus\Original\Characters\StringManager;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\JSONMapper;
use CouponsPlus\Original\Collections\Mapper\Types;

Class AmountValidator
{
    protected $options;
    protected $actualAmount;

    public static function getOptions() : Collection
    {
        return new Collection([
            'quantity' => [
                'type' => Types::STRING()->allowed([
                    __('Exactly', 'coupons-plus-international') => 'equals', 
                    __('At least', 'coupons-plus-international') => 'minimum', 
                    __('Maximum', 'coupons-plus-international') => 'maximum', 
                    __('In range', 'coupons-plus-international') => 'range',                
                ]),
                'amount' => Types::FLOAT()->meta([
                    'name' => '' # no name
                ]),
                'range' => [
                    'minimum' => Types::FLOAT()->meta([
                        'name' => __('From', 'coupons-plus-international')
                    ]),
                    'maxmimum' => Types::FLOAT()->meta([
                        'name' => __('To', 'coupons-plus-international')
                    ])
                ],
            ]
        ]);
    }
    
    public function __construct(array $options)
    {
        (object) $JSONMapper = new JSONMapper(static::getOptions()->asArray());

        $this->options = $JSONMapper->smartMap($options);
    }

    public function setAmount(float $actualAmount)
    {
        $this->actualAmount = $actualAmount;
    }
    
    public function isValid() : bool
    {
        switch ($this->options->quantity->type) {
            case 'equals':
                // float comparison
                return $this->compare($this->options->quantity->amount) === 0;
                break;
            case 'minimum':
                return $this->isMinimum($this->options->quantity->amount);
                break;
            case 'maximum':
                return $this->isMaximum($this->options->quantity->amount);
                break;
            case 'range':
                return $this->isMinimum($this->options->quantity->range->minimum) 
                        && 
                       $this->isMaximum($this->options->quantity->range->maxmimum);
                break;
        }
    }

    public function getType() : StringManager
    {
        return $this->options->quantity->type;
    }

    public function typeIs(string $type) : bool
    {
        return $this->options->quantity->type->is($type);
    }

    public function expects(string $quantityType) : bool
    {
        return $this->options->quantity->type->is($quantityType);   
    }

    public function getExpectedAmount() : float
    {
        return $this->options->quantity->amount;
    }
    
    public function getExpectedRangeAmount(string $rangeType) : float
    {
        return $this->options->quantity->range->{$rangeType};
    }

    public function isMinimum(float $minimumAmount) : bool
    {
        return $this->compare($minimumAmount) <= 0;
    }

    public function isMaximum(float $maximumAmount) : bool
    {
        return $this->compare($maximumAmount) >= 0;
    }

    protected function compare(float $expectedAmount) : int
    {
        return bccomp($expectedAmount, $this->actualAmount, 2);   
    }
    
}