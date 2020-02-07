<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Kalnoy\Nestedset\NodeTrait;

/**
 * Class Group
 * @package App
 *
 * @property int $id
 * @property int $added_by
 * @property User|null $addedBy
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @see https://github.com/lazychaser/laravel-nestedset for tree stuff
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
        return $this->recursiveProperty($this, collect(), 'users');
    }

    /**
     * The logos that are associated with this group or any descendant group
     *
     * @return Collection
     */
    public function logosBelow()
    {
        return $this->recursiveProperty($this, collect(), 'logos');
    }

    /**
     * The property of this group and any descendant groups
     *
     * @param  Group  $group
     * @param  Collection  $properties
     * @param  string  $propertyName
     *
     * @return Collection
     */
    private function recursiveProperty(Group $group, Collection $properties, string $propertyName)
    {
        if (is_iterable($group->$propertyName)) {
            foreach ($group->$propertyName as $property) {
                $properties->add($property);
            }
        } else {
            $properties->add($group->$propertyName);
        }

        foreach ($group->children as $child) {
            $this->recursiveProperty($child, $properties, $propertyName);
        }

        return $properties;
    }
}
