<?php

namespace App;

use App\Notifications\AccountCreatedNotification;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use \Spatie\WelcomeNotification\ReceivesWelcomeNotification;

/**
 * Class User
 * @package App
 *
 * @property int $id
 * @property string first_name
 * @property string last_name
 * @property string email
 * @property Carbon $email_verified_at
 * @property string $password
 * @property int $added_by
 * @property User|null $addedBy
 * @property int $managed_by
 * @property Group|null $managedBy
 * @property int|null $default_logo
 * @property Logo|null $defaultLogo
 * @property bool $super_admin
 * @property string $lang possible values in User::LANGUAGES
 * @property int $login_count
 * @property-read bool $is_admin
 * @property Carbon|null $last_login
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;
    use ReceivesWelcomeNotification;

    public const LANG_EN = 'en';
    public const LANG_DE = 'de';
    public const LANG_FR = 'fr';

    public const LANGUAGES = [self::LANG_EN, self::LANG_DE, self::LANG_FR];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'managed_by',
        'default_logo',
        'super_admin',
        'lang',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'email_verified_at',
        'last_login',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'super_admin' => 'boolean'
    ];

    /**
     * Expose those accessors
     *
     * @var array
     */
    protected $appends = [
        'is_admin'
    ];

    /**
     * The user that created this user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    /**
     * This users default logo. May be null.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function defaultLogo()
    {
        return $this->belongsTo(Logo::class, 'default_logo');
    }

    /**
     * The group that is responsible for this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function managedBy()
    {
        return $this->belongsTo(Group::class, 'managed_by');
    }

    /**
     * The users roles. Can be empty.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roles()
    {
        return $this->hasMany(Role::class);
    }

    /**
     * The users roles where he is admin. Can be empty.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function adminRoles()
    {
        return $this->roles()->admin();
    }

    /**
     * Does the user has any admin roles at all?
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->super_admin ? true : $this->adminRoles()->exists();
    }

    /**
     * Expose the is admin property
     *
     * @return bool
     */
    public function getIsAdminAttribute(): bool
    {
        return $this->isAdmin();
    }

    /**
     * All users that are managed by a group this user has an admin role for or
     * that are managed by a descendant group this user has an admin role for.
     *
     * @return \Illuminate\Support\Collection
     */
    public function manageableUsers()
    {
        if ($this->super_admin) {
            return collect(User::all());
        }

        $users = collect();
        foreach ($this->adminRoles()->get() as $role) {
            $users->push($role->usersBelow());
        }

        $users->push(Auth::user());

        return $users->flatten();
    }

    /**
     * Users can manage any groups they have an admin role for als well as
     * their descendant groups.
     *
     * @param  int|Group  $group  group id or group object
     *
     * @return bool
     */
    public function canManageGroup($group)
    {
        if ($this->super_admin) {
            return true;
        }

        foreach ($this->adminRoles()->get() as $role) {
            if ($role->isGroupBelow($group)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Users can use all groups and child groups they have an admin role for.
     * However if the only have a regular role (not admin), they cannot use the
     * child groups of the role.
     *
     * @param  int|Group  $group  group id or group object
     *
     * @return bool
     */
    public function canUseGroup($group)
    {
        if ($this->super_admin) {
            return true;
        }

        $group = is_int($group) ? Group::find($group) : $group;

        foreach ($this->roles()->get() as $role) {
            if ($role->admin && $role->isGroupBelow($group)) {
                return true;
            } elseif ($role->group->is($group)) {
                return true;
            }
        }

        return false;
    }

    /**
     * This user can use all logos that are associated with groups this user can
     * use.
     *
     * @param  int|Logo  $logo  logo id or logo object
     *
     * @return bool
     */
    public function canUseLogo($logo)
    {
        if ($this->super_admin) {
            return true;
        }

        $logo = is_int($logo) ? Logo::find($logo) : $logo;

        foreach ($logo->groups as $group) {
            if ($this->canUseGroup($group)) {
                return true;
            }
        }

        return false;
    }

    /**
     * This user can manage all logos that are associated with groups this user
     * can manage.
     *
     * @param  int|Logo  $logo  logo id or logo object
     *
     * @return bool
     */
    public function canManageLogo($logo)
    {
        if ($this->super_admin) {
            return true;
        }

        $logo = is_int($logo) ? Logo::find($logo) : $logo;

        foreach ($logo->groups as $group) {
            if ($this->canManageGroup($group)) {
                return true;
            }
        }

        return false;
    }

    /**
     * The groups this user can manage
     *
     * @return Collection
     */
    public function manageableGroups()
    {
        $groups = [];
        foreach ($this->adminRoles as $role) {
            $groups[] = Group::with('descendants')
                             ->where('id', $role->group_id)
                             ->first();
        }

        return collect($groups);
    }

    /**
     * The logos this user can manage
     *
     * @return Collection
     */
    public function manageableLogos()
    {
        if ($this->super_admin) {
            return Logo::all();
        }

        $groups = $this->manageableGroups();

        $logos = collect();
        foreach ($groups as $group) {
            foreach ($group->logosBelow() as $logo) {
                if ($logos->where('id', $logo->id)->isEmpty()) {
                    $logos->add($logo);
                }
            }
        }

        return $logos;
    }

    /**
     * Send the onboarding email
     *
     * @param  \Carbon\Carbon  $validUntil
     */
    public function sendWelcomeNotification(\Carbon\Carbon $validUntil)
    {
        $lang = App::getLocale();
        App::setLocale($this->lang);

        $this->notify(new AccountCreatedNotification($validUntil));

        App::setLocale($lang);
    }
}
