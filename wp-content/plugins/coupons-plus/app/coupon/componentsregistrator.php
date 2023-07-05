<?php

namespace CouponsPlus\App\Coupon;

use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\JSONMapper;
use CouponsPlus\Original\Collections\Mapper\Types;
use CouponsPlus\Original\Environment\Env;
use CouponsPlus\Original\Utilities\TypeChecker;

Abstract Class ComponentsRegistrator
{
    use TypeChecker;

    #protected static $instance;

    protected $components = []; 

    abstract static protected function getComponentId() : string;
    abstract static protected function getComponentType() : string;

    public static function createFromOptions($options, \WC_Coupon $coupon)
    {
        (object) $JSONMapper = new JSONMapper([
            'type' => Types::STRING()->allowed(static::get()->all()->map(function(string $Component) : string {
                return $Component::TYPE;
            })),
            'options' => Types::ANY
        ]);

        (object) $preOptions = $JSONMapper->smartMap($options);

        (string) $Component = static::get()->all()->get($preOptions->type);

        return static::create($Component, $preOptions->options, $coupon);
    }

    protected static function create(string $Component, $preOptionsOptions, \WC_Coupon $coupon)
    {
        return new $Component($preOptions->options);    
    }

    public static function get()
    {
        if (!static::$instance) {
            static::$instance = new static;
        }

        return static::$instance;   
    }

    public function all()
    {
        return new Collection($this->components);   
    }
    
    protected function __construct()
    {
        do_action(Env::idLowerCase(). '_register_'.static::getComponentId().'_component', $this);
    }

    public function register(string $Component)
    {
        $this->components[$Component::TYPE] = $this->expect($Component)
                                           ->toBe(static::getComponentType());
    }
}