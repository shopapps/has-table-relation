<?php

namespace ShopApps\HasTableRelation;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

trait HasTable
{
    public function hasTable($related)
    {
        $instance = new $related;

        return new class($instance->newQuery(), $this) extends Relation {
            public function addConstraints()
            {
                // No constraints needed, we want all records
            }

            public function addEagerConstraints(array $models)
            {
                // No constraints needed, we want all records
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
                return $this->query->get();
            }
        };
    }
}
