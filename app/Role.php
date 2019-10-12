<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class Role
 * @package App
 *
 * @property int $id
 * @property int $group_id
 * @property Group|null $group
 * @property int $user_id
 * @property User|null $user
 * @property int $added_by
 * @property User|null $addedBy
 * @property bool $admin
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Role extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'group_id',
        'admin',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'admin' => 'boolean'
    ];

    /**
     * This roles user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * This roles group
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * The user that created this role
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    /**
     * Scope a query to only include admin roles.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAdmin($query)
    {
        return $query->where('admin', true);
    }

    /**
     * The users attached to this roles group and any descending groups
     *
     * @return \Illuminate\Support\Collection
     */
    public function usersBelow()
    {
        return $this->group->usersBelow();
    }

    /**
     * Check if the given group is the group of this role or any descendant of
     * the group of this role.
     *
     * @param  int|Group  $group  group id or group object
     *
     * @return bool
     */
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
