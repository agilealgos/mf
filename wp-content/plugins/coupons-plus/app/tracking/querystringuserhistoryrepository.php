<?php

namespace CouponsPlus\App\Tracking;

use CouponsPlus\Original\Collections\Collection;
use Delight\Cookie\Cookie;

Class QueryStringUserHistoryRepository
{
    const QUERY_ID = 'cpqsuh';

    protected $uniqueId;
    protected $parameters;

    public static function updateParameters()
    {
        global $wp_query;

        (object) $self = new static(static::QUERY_ID);

        (object) $newParameters = $self->getParameters()->merge($wp_query->query_vars);

        (object) $ck = new Cookie(static::QUERY_ID);
        (integer) $numberOfDays = 7;

        $ck->setValue(http_build_query($newParameters->asArray()))
           ->setMaxAge(60*60*24*$numberOfDays) // right now this one should be stored forever
           ->saveAndSet();
    }

    public function __construct(string $uniqueId)
    {
        $this->uniqueId = $uniqueId;
        $this->parameters = $this->loadParameters();
    }

    public function getValueForParamater(string $parameterName) : string
    {
        return $this->getParameters()->get($parameterName) ?? '';
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    protected function loadParameters() : Collection
    {
        (array) $parameters = [];

        parse_str(Cookie::get($this->uniqueId), $parameters);

        return new Collection($parameters);
    }
}