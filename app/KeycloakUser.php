<?php


namespace App;


use Vizir\KeycloakWebGuard\Facades\KeycloakWeb;

class KeycloakUser extends \Vizir\KeycloakWebGuard\Models\KeycloakUser
{
    protected $fillable = [
        'given_name',
        'family_name',
        'email',
        'locale',
        'groups'
    ];

    public function toArray() {
        return $this->attributes;
    }
}
