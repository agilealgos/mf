<?php

namespace CouponsPlus\App\Offers;

use CouponsPlus\App\Offers\Offer;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Environment\Env;
use CouponsPlus\Original\Utilities\TypeChecker;

Class OffersRegistrator
{
    use TypeChecker;

    private static $instance;

    private $offers = []; 

    public static function get()
    {
        if (!static::$instance) {
            static::$instance = new static;
        }

        return static::$instance;   
    }

    public function all()
    {
        return new Collection($this->offers);   
    }
    
    protected function __construct()
    {
        do_action(Env::idLowerCase(). '_register_offer_component', $this);
    }

    public function register(string $Offer)
    {
        $this->offers[$Offer::TYPE] = $this->expect($Offer)
                                           ->toBe(Offer::class);
    }
}