<?php

namespace CouponsPlus\Original\Data\Model;

use BadMethodCallException;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Data\Drivers\DatabaseDriver;
use CouponsPlus\Original\Data\Schema\DatabaseTable;
use CouponsPlus\Original\Utilities\TypeChecker;
use CouponsPlus\Original\Cache\MemoryCache;
use CouponsPlus\Original\Characters\StringManager;

Abstract Class Gateway
{
    use TypeChecker;

    protected $table;
    protected $domainType;

    protected $cache;
    
    abstract protected function model();

    public function __construct(DatabaseDriver $driver, $bind = null)
    {
        $this->table = $this->expect($this->model()['table'])->toBe(DatabaseTable::class);
        $this->domainType = $this->model()['domain'];

        $this->cache = new MemoryCache([]);

        $this->driver = $driver;
        $this->valueToBind = $bind;
    }

    public function insert(Domain $domain)
    {
        $domain->prepareForInsertion();

        return $this->driver->execute(
            "INSERT INTO {$this->table->getName()} ({$this->getFields($domain)}) VALUES({$this->getValuesAsMark($domain)})", 
            $domain->getAvailableValues()
        );
    }

    public function update(Array $data)
    {
        (object) $data = new Collection($data);

        (string) $primaryKey = $this->table->getPrimaryKey();
        (object) $dataToUpdate = $data->only($this->table->getFieldNames())->except([$primaryKey]);

        if (!$data->hasKey($primaryKey)) throw new BadMethodCallException("Please specify the primary key value to limit the update to.");

        if ($dataToUpdate->haveNone()) throw new BadMethodCallException("Please specify the data to update.");

        (string) $fields = $dataToUpdate->getKeys()->reduce(function($refValue, StringManager $key) use($dataToUpdate) {
            (string) $placeHolder = is_null($dataToUpdate->get((string) $key))? 'NULL' : '?';
            // we'll make sure only valid registered fields for the table are used
            (string) $field = $this->table->getField($key->getAlphanumeric());

            $refValue .= $field? "{$field} = {$placeHolder}, " : '';

            return $refValue;
        })->trim(', ');
            
        return $this->driver->execute(
            "UPDATE {$this->table->getName()} SET {$fields} WHERE {$primaryKey} = ? LIMIT 1", 
            $dataToUpdate->except($dataToUpdate->filter('is_null')->getKeys())
                         ->resetKeys()
                         ->push($data->get($primaryKey))->asArray()
        );
    }

    public function delete(Domain $domain)
    {
        (string) $primaryKey = $this->table->getPrimaryKey();

        return $this->driver->execute(
            "DELETE FROM {$this->table->getName()} 
             WHERE {$primaryKey} = ? 
             LIMIT 1",
             [$domain->{$primaryKey}]
        );
    }

    public function getAll()
    {
        return $this->createCollection(
            $this->driver->execute(
                "SELECT * FROM {$this->table->getName()} ORDER BY {$this->table->getPrimaryKey()} DESC"
            )
        );
    }
    
    public function fieldWithValueExists(Array $field)
    {
        (string) $fieldName = $this->table->getField($field['name']);

        return $this->createCollection(
            (array) $this->driver->get(
                "SELECT {$this->table->getPrimaryKey()} 
                 FROM {$this->table->getName()} 
                 WHERE {$fieldName} = ?", 
                [
                    $field['value']
                ]
            )
        )->haveAny();
    }

    public function hasFieldWithValue(Array $field)
    {
        (string) $fieldName = $this->table->getField($field['name']);

        return $this->createCollection(
            $this->driver->get(
                "SELECT {$this->table->getPrimaryKey()} 
                 FROM {$this->table->getName()} 
                 WHERE {$this->table->getPrimaryKey()} = ? and {$fieldName} = ?", 
                [
                    $field[$this->table->getPrimaryKey()],
                    $field['value']
                ]
            )
        )->haveAny();
    }

    public function createCollection(array $set)
    {
        return (new Collection($set))->map(function($entity) {
            (string) $Domain = $this->domainType;

            (object) $domain = new $Domain($entity, $this->valueToBind);

            return $domain;
        });
    }

    protected function getFields(Domain $domain)
    {
        return trim(implode(', ', $domain->getAvailableFields()), ', ');
    }

    protected function getValuesAsMark(Domain $domain)
    {
        return trim(str_repeat('?, ',  count($domain->getAvailableValues())), ', ');
    }

}