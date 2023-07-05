<?php

namespace CouponsPlus\App\Validators;

use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\JSONMapper;

Abstract Class Validator implements Validatable
{
    protected $options;

    abstract static public function getOptions() : Collection;
    abstract public function isValid() : bool;
    
    public function __construct(array $options)
    {
        (object) $JSONMapper = new JSONMapper(static::getOptions()->asArray());

        $this->options = $JSONMapper->smartMap($options);
    }
}