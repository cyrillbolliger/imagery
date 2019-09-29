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

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login'        => 'datetime',
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
}
