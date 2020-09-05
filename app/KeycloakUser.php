<?php


namespace App;


class KeycloakUser extends \Vizir\KeycloakWebGuard\Models\KeycloakUser
{
    protected $fillable = [
        'given_name',
        'family_name',
        'email',
        'locale'
    ];
}
