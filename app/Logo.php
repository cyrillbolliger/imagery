<?php

namespace App;

use App\Exceptions\LogoException;
use App\Logo\LogoFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

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
        return route('logo', ['logo' => $this->id, 'color' => 'light']);
    }

    public function getSrcGreenAttribute()
    {
        return route('logo', ['logo' => $this->id, 'color' => 'dark']);
    }

    public function getGroupsAttribute()
    {
        return $this->groups()->select('groups.id')->get()->pluck('id');
    }

    /**
     * @param  array  $args
     * @return string
     */
    public function getRelPath(array $args = []): string
    {
        $defaults = [
            \App\Logo\Logo::LOGO_COLOR_DARK,
            config('app.logo_width'),
        ];

        [$color, $width] = array_merge($args, $defaults);

        try {
            $logo = LogoFactory::get($this->type, $color, [$this->name]);
            return $logo->getPng($width);
        } catch (Exceptions\LogoException $e) {
            if ($e->getCode() === LogoException::OVERSIZE) {
                abort(422, $e->getMessage());
            }

            Log::warning($e);
            return '';
        }
    }

    /**
     * @return string
     * @throws Exceptions\LogoException
     */
    public function getRelThumbPath(): string
    {
        return $this->getRelPath(\App\Logo\Logo::LOGO_COLOR_DARK);
    }
}
