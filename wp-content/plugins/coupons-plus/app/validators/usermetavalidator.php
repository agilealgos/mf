<?php

namespace CouponsPlus\App\Validators;

use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\Mapper\Types;
use WC_Customer;

Class UserMetaValidator extends FieldValidator
{
    protected function getFieldValue()
    {
        (object) $customer = new WC_Customer(get_current_user_id());
        
        return $customer->get_meta($this->options->name->get());
    }
}

