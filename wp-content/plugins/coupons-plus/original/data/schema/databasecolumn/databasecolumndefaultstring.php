<?php

namespace CouponsPlus\Original\Data\Schema\DatabaseColumn;

use CouponsPlus\Original\Data\Schema\DatabaseColumn\DatabaseColumnDefault;

Class DatabaseColumnDefaultString extends DatabaseColumnDefault
{
    public function getDefinition()
    {
        return "DEFAULT '{$this->getCleanValue()}'";
    }
}
