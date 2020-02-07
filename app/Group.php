<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Kalnoy\Nestedset\NodeTrait;

/**
 * Class Group
 *
 * @package App
 * @property int $id
 * @property int $added_by
 * @property User|null $addedBy
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int $_lft
 * @property int $_rgt
 * @property int|null $parent_id
 * @property-read \Kalnoy\Nestedset\Collection|\App\Group[] $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Logo[] $logos
 * @property-read int|null $logos_count
 * @property-read \App\Group|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @property-read int|null $users_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Role[] $roles
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group d()
 * @method static bool|null forceDelete()
 * @method static \Kalnoy\Nestedset\QueryBuilder|\App\Group newModelQuery()
 * @method static \Kalnoy\Nestedset\QueryBuilder|\App\Group newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Group onlyTrashed()
 * @method static \Kalnoy\Nestedset\QueryBuilder|\App\Group query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereLft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereRgt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Group withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Group withoutTrashed()
 * @method static descendantsAndSelf(int $id)
 * @mixin \Eloquent
 */
class Group extends Model
{
    use SoftDeletes;
    use NodeTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id',
        'name',
    ];

    /**
     * The logos associated with this group
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function logos()
    {
        return $this->belongsToMany(Logo::class);
    }

    /**
     * The user that created this group
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    /**
     * The users that are managed by this group
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class, 'managed_by');
    }

    /**
     * The users that are managed by this group or any descendant group
     *
     * @return Collection
     */
    public function usersBelow()
    {
        return $this->getBelow('users');
    }

    /**
     * The roles associated with this group
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roles()
    {
        return $this->hasMany(Role::class);
    }

    /**
     * The logos that are associated with this group or any descendant group
     *
     * @return Collection
     */
    public function logosBelow()
    {
        return $this->getBelow('logos');
    }

    /**
     * Return a flat list of the given property of this instance and all its
     * descendants
     *
     * @param  string  $propertyName
     *
     * @return Collection
     */
    private function getBelow(string $propertyName)
    {
        /** @var Group[] $groups */
        $groups     = Group::descendantsAndSelf($this->id);
        $collection = collect();

        foreach ($groups as $group) {
            $collection = $group->getPropertyFlat($collection, $propertyName);
        }

        return $collection;
    }

    /**
     * The property of this group in a flat collection
     *
     * @param  Collection  $properties
     * @param  string  $propertyName
     *
     * @return Collection
     */
    private function getPropertyFlat(Collection $properties, string $propertyName)
    {
        if (is_iterable($this->$propertyName)) {
            foreach ($this->$propertyName as $property) {
                $properties->add($property);
            }
        } else {
            $properties->add($this->$propertyName);
        }

        return $properties;
    }
}
