<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login'        => 'datetime',
        'super_admin'       => 'boolean'
    ];

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function defaultLogo()
    {
        return $this->belongsTo(Logo::class, 'default_logo');
    }

    public function managedBy()
    {
        return $this->belongsTo(Group::class, 'managed_by');
    }

    public function roles()
    {
        return $this->hasMany(Role::class);
    }

    public function isAdmin(): bool
    {
        return $this->super_admin ? true : $this->roles()->admin()->exists();
    }

    public function manageableUsers()
    {
        if ($this->super_admin) {
            return User::all();
        }

        $users = collect();
        foreach ($this->roles()->admin()->get() as $role) {
            $users->push($role->manageableUsers());
        }

        return $users->flatten();
    }
}
