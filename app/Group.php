<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use SoftDeletes;

    public function parent()
    {
        return $this->belongsTo(Group::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Group::class, 'parent_id');
    }

    public function logos()
    {
        return $this->belongsToMany(Logo::class);
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function isDescendantOf($possibleParent): bool
    {
        $possibleParent = is_int($possibleParent) ? $possibleParent : $possibleParent->id;

        return $this->isChildOfRecursive($possibleParent, $this);
    }

    private function isChildOfRecursive(int $possibleParentId, $group): bool
    {
        if ( ! $group) {
            // group was deleted
            return false;
        }

        if ($group->parent_id === $possibleParentId) {
            return true;
        }

        if ($group->id === $group->parent_id) {
            // we reached the root group
            return false;
        }

        return $this->isChildOfRecursive($possibleParentId, $group->parent);
    }
}
