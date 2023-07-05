<?php

namespace CouponsPlus\Original\Events\Handler;

Abstract Class GlobalEventsValidator
{
    abstract public function canBeExecuted() : bool;
}