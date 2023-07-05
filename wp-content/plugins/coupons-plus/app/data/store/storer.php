<?php

namespace CouponsPlus\App\Data\Store;

use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Environment\Env;

Abstract class Storer
{
    abstract protected function getPostFieldName() : string;
    abstract protected function storeData();

    protected function canBeStored() : bool
    {
        // should be isset 
        // checks if the variable is set
        // and that the nonce is valid.
        return (!empty(
                    sanitize_text_field(wp_unslash($_POST[$this->getPostFieldName()] ?? ''))
                ) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST[$this->getNonceMeta()->get('fieldName')] ?? '')), $this->getNonceMeta()->get('id'))) || Env::isTesting();
    }

    /**
     * Can be overridden by child classes if needed.
     */
    protected function getNonceMeta() : Collection
    {
        return new Collection([
            'fieldName' => Env::getWithPrefix('dashboard_nonce'),
            'id' => 'coupons-plus-dashboard'
        ]);   
    }

    protected function getData()
    {
        // before data is used it's checked for validity in static::canBeStored()
        // the aforementioned method checks both that the variable is set and that the
        // nonce is valid.
        return sanitize_text_field(wp_unslash($_POST[$this->getPostFieldName()] ?? ''));
    }
    
    public function store()
    {
        if ($this->canBeStored()) {
            $this->storeData();
        }
    }
    
}