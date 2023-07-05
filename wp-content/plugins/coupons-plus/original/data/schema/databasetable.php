<?php

namespace CouponsPlus\Original\Data\Schema;

use CouponsPlus\Original\Characters\StringManager;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Data\Schema\DatabaseColumn;
use CouponsPlus\Original\Data\Schema\DatabaseColumnChanges;
use CouponsPlus\Original\Utilities\TypeChecker;
use CouponsPlus\Original\Utilities\className;

Abstract Class DatabaseTable
{
    use className;
    use TypeChecker;

    protected $name;
    protected $fields = [];
    protected $primary;

    abstract protected function name();
    abstract protected function fields();   
    abstract protected function changes();
    /*Changes: for adition, a field with the name must also be present in the fields() method,
               for deduction the field to remove must NOT be present in the fields() method,
               transforms: currently unavailable
    */

    public function __construct()
    {
        $this->name = strtolower($this->name());
        $this->fields = (array) $this->expectEach($this->fields())->toBe(DatabaseColumn::class);
        $this->primary = $this->fields['primary'];
    }

    public function getName()
    {
        return (new StringManager($this->name))->getAlphanumeric();
    }

    public function getFields()
    {
        return new Collection((array) $this->fields);
    }

    public function getFieldNames()
    {
        return $this->getFields()->map(function(DatabaseColumn $field) {
            return $field->getName();
        });
    }

    public function getField($fieldName)
    {
        /*mixed*/ $field = $this->getFields()->filter(function(DatabaseColumn $field) use ($fieldName) {
            return $field->getName() === (string) $fieldName;
        })->first();

        return $field? $field->getName() : null;
    }

    public function getPrimaryKey()
    {
        return $this->primary->getName();
    }

    public function map(array $fields)
    {
        return new DatabaseTableMapper($this, $fields);
    }

    public function getFieldsDefinition()
    {
        return implode(', ', array_map(function(DatabaseColumn $field) {
            return $field->getDefinition();
        }, $this->fields));
    }

    public function additions()
    {
        return array_filter($this->getChanged('additions'), function(DatabaseColumn $fieldToAdd){
            return $this->hasFieldWithName($fieldToAdd->getName());
        });
    }

    public function deductions()
    {
        return array_filter($this->getChanged('deductions'), function(DatabaseColumn $fieldToRemove){
            return !$this->hasFieldWithName($fieldToRemove->getName());
        });
    }

    /*
     Changing a column name and then later on adding another one with the old name is
     not supported and may introduce some serious bugs.
     */
    public function transforms()
    {
        return array_filter($this->getChanged('transforms'), function(DatabaseColumnChanges $columnChanges){
            return $this->hasFieldWithName($columnChanges->getNewColumn()->getName());
        });
    }

    public function getChanged($type)
    {
        return isset($this->changes()[$type])? $this->changes()[$type] : [];
    }

    public function hasFieldWithName($name)
    {
        foreach ($this->getFields()->asArray() as $field) {
            if ($field->getName() === $name) {
                return true;
            }
        }
    }

}