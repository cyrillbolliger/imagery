<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class Logo
 * @package App
 *
 * @property int $id
 * @property int $added_by
 * @property User|null $addedBy
 * @property string $filename
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
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

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
