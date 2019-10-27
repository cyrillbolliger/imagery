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
 * @property-write string $filename
 * @property-read string $src
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Logo extends Model implements FileModel
{
    use SoftDeletes;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'filename',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'src',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

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

    public function getSrcAttribute()
    {
        return route('logo', ['logo' => $this->id]);
    }

    public function getPath()
    {
        return disk_path(self::getStorageDir()).DIRECTORY_SEPARATOR.$this->filename;
    }

    public static function getStorageDir()
    {
        return config('app.logo_dir');
    }
}
