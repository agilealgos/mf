<?php

namespace CouponsPlus\Original\Collections;

use CouponsPlus\Original\Utilities\TypeChecker;

Abstract Class TypedCollection extends Collection
{
    use TypeChecker;

    public function __construct(/*Array|Collection*/ $elements)
    {
        parent::__construct($elements);

        $this->elements = $this->expectEach($this->elements)->toBe(static::TYPE);
    }
    
}