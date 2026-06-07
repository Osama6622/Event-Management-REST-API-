<?php

namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;


trait CanLoadRelationships {
    public function loadRelationships(
        Model|QueryBuilder|EloquentBuilder $for,
        ?array $relations = null
    ): Model|QueryBuilder|EloquentBuilder {
        $relations = $relations ?? $this->relations ?? [];

        foreach($relations as $relation) {
            $for->when(
                $this->shouldIncludeRelation($relation),
                fn($q) => $for instanceof Model ? $for->load($relation) : $for->with($relation)
            );
        }

        return $for;
    }

    protected function shouldIncludeRelation(String $relation):bool {
        $include = request()->query('include');
        if(!$include) {
            return false;
        }

        $relations = array_map('trim', explode(',', $include));

       return in_array($relation, $relations);
    }
}