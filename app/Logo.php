<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Logo extends Model
{
    use SoftDeletes;

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class);
    }

    public function added_by()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
