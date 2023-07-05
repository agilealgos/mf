<?php

namespace CouponsPlus\App\Export;

use CouponsPlus\App\Export\Abilities\OptionsMapper;

Class StructureMapper
{
    protected $optionsMapper;

    public function __construct(OptionsMapper $optionsMapper)
    {
        $this->optionsMapper = $optionsMapper;
    }

    public function getMap() : array
    {
        (object) $optionsMap = $this->optionsMapper->getOptions();
    }
}
