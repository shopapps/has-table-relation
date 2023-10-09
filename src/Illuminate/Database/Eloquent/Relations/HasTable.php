<?php

namespace ShopApps\HasTableRelation\Illuminate\Database\Eloquent\Relations;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Concerns\InteractsWithDictionary;
use Illuminate\Database\Eloquent\Relations\Relation;
use ShopApps\HasTableRelation\Traits\HasOrg;

class HasTable extends Relation
{
    use InteractsWithDictionary;
    use HasOrg;
    
    public function __construct(Builder $query, Model $parent)
    {
        parent::__construct($query, $parent);
    }
    public function addConstraints()
    {
        // No constraints needed, we want all records
    }
    
    public function addEagerConstraints(array $models)
    {
        // No constraints needed, we want all records
    }
    protected function setForeignAttributesForCreate(Model $model)
    {
        // No foreign key needed, we want all records
    }
    
    public function initRelation(array $models, $relation)
    {
        foreach ($models as $model) {
            $model->setRelation($relation, $this->related->newCollection());
        }
        
        return $models;
    }
    
    public function match(array $models, \Illuminate\Database\Eloquent\Collection $results, $relation)
    {
        foreach ($models as $model) {
            $model->setRelation($relation, $results);
        }
        
        return $models;
    }
    
    public function getResults()
    {
        return ! is_null($this->getParentKey())
            ? $this->query->get()
            : $this->related->newCollection();
    }
}
