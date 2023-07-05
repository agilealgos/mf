<?php

namespace CouponsPlus\App\Export\Abilities;

use CouponsPlus\Original\Collections\Collection;

Interface OptionsMapper
{
    public function getOptions() : Collection;
}