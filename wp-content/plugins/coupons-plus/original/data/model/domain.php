<?php

namespace CouponsPlus\Original\Data\Model;

use CouponsPlus\Original\Cache\MemoryCache;
use CouponsPlus\Original\Collections\JSONMapper;
use CouponsPlus\Original\Collections\Mapper\Types\BooleanType;
use CouponsPlus\Original\Utilities\ObjectSetter;
use ReflectionObject;
use ReflectionProperty;

Class Domain
{
    protected $cache;
    protected static $convertBooleansToStringRepresentations = true;

    public static function fromJson($jsonString)
    {
        return new static(JSONMapper::getArrayFromJson($jsonString));
    }

    public function __construct(Array $values = [], $valueToBind = null)
    {
        $this->cache = new MemoryCache;

        if (method_exists($this, 'map')) {
            $values = $this->buildMap($values);
        }

        ObjectSetter::setPublicValues(['object' => $this, 'values' => $values]);

        if (method_exists($this, 'setUp')) {
            $valueToBind? $this->setUp($valueToBind) : $this->setUp();
        }
    }

    public function buildMap(Array $values)
    {
        return (new JSONMapper($this->map($values)))->smartMap($values)->asArray();   
    }

    public function getAvailableFields()
    {
        return array_keys($this->getAvailableValues());
    }

    public function getAvailableValuesBut(array $fieldsToExclude)
    {
        return array_diff_key($this->getAvailableValues(), array_flip($fieldsToExclude));
    }

    /**
     * Gets exportable scalar values by default. StringManager and Collection instances are converted to native strings.
     * @return array
     */
    public function getAvailableValues($convertNonScalarToScalar = true)
    {
        (object) $reflection = new ReflectionObject($this);

        (array) $publicProperties = [];

        foreach($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $publicProperty) {
            if (!$publicProperty->isStatic()) {
                $value = $this->{$publicProperty->name};
                $publicProperties[$publicProperty->name] = $convertNonScalarToScalar? $this->getScalarValue($value) : $value;
            }
        }

        return $publicProperties;
    }

    public function prepareForInsertion()
    {
        if (method_exists($this, 'beforeInsertion')) {
            $this->beforeInsertion();
        }
    }

    public function prepareForUpdate()
    {
        if (method_exists($this, 'beforeUpdate')) {
            $this->beforeUpdate();
        }
    }

    protected function getScalarValue($value)
    {

        if (is_object($value) && (method_exists($value, '__toString') || method_exists($value, 'asStringRepresentation'))) {
            if (method_exists($value, 'asStringRepresentation')) {
                return (string) $value->asStringRepresentation();
            }
            return (string) $value;
        } elseif (is_object($value) || is_array($value)) {
            return '';
        } elseif (is_bool($value) && static::$convertBooleansToStringRepresentations) {
            return BooleanType::exportValue($value);
        }

        return $value;
    }

}