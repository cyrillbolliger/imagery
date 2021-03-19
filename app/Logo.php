<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class Logo
 *
 * @package App
 * @property int $id
 * @property int $added_by
 * @property User|null $addedBy
 * @property-write string $type
 * @property-read string $src_white
 * @property-read string $src_green
 * @property string $name
 * @property Group[] $groups
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read int|null $groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Image[] $images
 * @property-read int|null $images_count
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Logo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Logo newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Logo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Logo query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Logo whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Logo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Logo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Logo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Logo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Logo whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Logo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Logo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Logo withoutTrashed()
 * @mixin \Eloquent
 */
class Logo extends Model implements FileModel
{
    use SoftDeletes;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'src_white',
        'src_green',
        'groups',
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

    public function getSrcWhiteAttribute()
    {
        return route('logo', ['logo' => $this->id, 'color' => 'white']);
    }

    public function getSrcGreenAttribute()
    {
        return route('logo', ['logo' => $this->id, 'color' => 'green']);
    }

    public function getGroupsAttribute()
    {
        return $this->groups()->select('groups.id')->get()->pluck('id');
    }

    public function getRelPath($color = null)
    {
        return self::getStorageDir().DIRECTORY_SEPARATOR.$this->type.'-'.$color.'.svg';
    }

    public static function getStorageDir()
    {
        return create_dir(config('app.logo_dir'));
    }

    public function getRelThumbPath()
    {
        return self::getStorageDir().DIRECTORY_SEPARATOR.$this->type.'-green.svg';
    }
}
