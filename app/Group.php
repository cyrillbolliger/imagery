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

    public function added_by()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
