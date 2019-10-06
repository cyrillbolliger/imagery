<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function scopeAdmin($query)
    {
        return $query->where('admin', true);
    }

    public function usersBelow()
    {
        return $this->group->usersBelow();
    }

    public function groupsBelow()
    {
        return $this->group->descendants();
    }

    public function isGroupBelow($group)
    {
        if (is_int($group)) {
            $group = Group::find($group);
        }

        if ($this->group->is($group)) {
            return true;
        }

        if ($group->isDescendantOf($this->group)) {
            return true;
        }

        return false;
    }
}
