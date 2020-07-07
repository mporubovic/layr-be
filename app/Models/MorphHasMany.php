<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class MorphHasMany extends MorphTo
{
    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    public function getResults()
    {
        return $this->ownerKey ? $this->query->get() : null;
    }

    /**
     * Match the results for a given type to their parents.
     *
     * @param  string  $type
     * @param  \Illuminate\Database\Eloquent\Collection  $results
     * @return void
     */
    protected function matchToMorphParents($type, Collection $results)
    {
        $ownerKeyName = $this->ownerKey ?: $results->first()->getKeyName();

        foreach ($results->groupBy($ownerKeyName) as $ownerKey => $result) {
            if (isset($this->dictionary[$type][$ownerKey])) {
                foreach ($this->dictionary[$type][$ownerKey] as $model) {
                    $model->setRelation($this->relation, $result);
                }
            }
        }
    }
}