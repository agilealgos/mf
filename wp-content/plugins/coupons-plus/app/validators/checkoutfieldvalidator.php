<?php

namespace CouponsPlus\App\Validators;

use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\Mapper\Types;

Class CheckoutFieldValidator extends FieldValidator
{
    protected function getFieldValue()
    {
        return \WC()->checkout->get_value($this->options->name->get());   
    }
}