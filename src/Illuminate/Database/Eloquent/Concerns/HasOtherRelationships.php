<?php

namespace ShopApps\HasTableRelation\Illuminate\Database\Eloquent\Concerns;;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use ShopApps\HasTableRelation\Illuminate\Database\Eloquent\Relations\HasTable;
use ShopApps\HasTableRelation\Illuminate\Database\Eloquent\Relations\BelongsToTable;

trait HasOtherRelationships
{
    use HasRelationships;
    /**
     * Define a one-to-many relationship involving all records in a single table.
     *
     * @param  string  $related
     * @param  string|null  $foreignKey
     * @param  string|null  $localKey
     * @return ShopApps\HasTableRelation\Relations\HasTable
     */
    public function hasTable($related)
    {
        $instance = $this->newRelatedInstance($related);
        return $this->newHasTable($instance->newQuery(), $this);
    }

    protected function newHasTable(Builder $query, Model $parent)
    {
        return new HasTable($query, $parent);
    }

    public function BelongsToTable($related, $whichRow = 'first', $ownerKey = null, $relationName = null)
    {
        $instance = $this->newRelatedInstance($related);
        return $this->newBelongsToTable($instance->newQuery(), $this, $whichRow, $ownerKey, $relationName);
    }

    protected function newBelongsToTable(Builder $query, Model $parent, $whichRow = 'first', $ownerKey = null, $relationName = null)
    {
         switch($whichRow) {
            case 'all': $whichRow = 'get'; break;
        }
        return new BelongsToTable($query, $parent, $whichRow, $ownerKey, $relationName);
    }
}
