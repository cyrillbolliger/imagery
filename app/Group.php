<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Class Group
 * @package App
 *
 * @property int $id
 * @property int|null $parent_id
 * @property Group|null $parent
 * @property int $added_by
 * @property User|null $addedBy
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Group extends Model
{
    use SoftDeletes;

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
     * The parent group
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Group::class, 'parent_id');
    }

    /**
     * The direct children of this group
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(Group::class, 'parent_id')
                    ->where('id', '<>', $this->id);
    }

    /**
     * A tree with all descendants (recursive children) of this group
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function descendants()
    {
        return $this->children()
                    ->with('descendants');
    }

    /**
     * A tree (or lets say a root path) with all ancestors (recursive parents)
     * of this group
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ancestors()
    {
        return $this->parent()
                    ->with('ancestors');
    }

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
        if (is_array($group->$propertyName)) {
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

    /**
     * Check if the given group is an ancestor of this group.
     *
     * @param  int|Group  $possibleParent  group id or group object
     *
     * @return bool
     */
    public function isDescendantOf($possibleParent): bool
    {
        $possibleParent = is_int($possibleParent) ? $possibleParent : $possibleParent->id;

        return $this->isChildOfRecursive($possibleParent, $this);
    }

    /**
     * Recursively check if the given parent group is the parent of the given
     * group.
     *
     * @param  int  $possibleParentId
     * @param  int|Group  $group  group id or group object
     *
     * @return bool
     */
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
