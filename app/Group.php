<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Group extends Model
{
    use SoftDeletes;

    public function parent()
    {
        return $this->belongsTo(Group::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Group::class, 'parent_id')
                    ->where('id', '<>', $this->id);
    }

    public function descendants()
    {
        return $this->children()
                    ->with('descendants');
    }

    public function ancestors()
    {
        return $this->parent()
                    ->with('ancestors');
    }

    public function logos()
    {
        return $this->belongsToMany(Logo::class);
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'managed_by');
    }

    public function usersBelow()
    {
        return $this->usersRecursive($this, collect());
    }

    private function usersRecursive(Group $group, Collection $users)
    {
        foreach ($group->users as $user) {
            $users->add($user);
        }

        foreach ($group->children as $child) {
            $this->usersRecursive($child, $users);
        }

        return $users;
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
