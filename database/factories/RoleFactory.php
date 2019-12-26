<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Group;
use App\GroupLogo;
use App\Role;
use App\User;
use Faker\Generator as Faker;

$factory->define(Role::class, function (Faker $faker) {
    return [
        'group_id' => function () {
            return factory(GroupLogo::class)->create()->group_id;
        },
        'user_id'  => function () {
            return factory(User::class)->create()->id;
        },
        'added_by' => User::first() ? User::first()->id : 1,
        'admin'    => false
    ];
});

$factory->state(Role::class, 'root', [
    'group_id' => User::first() ? User::first()->id : 1,
    'user_id'  => User::first() ? User::first()->id : 1,
    'admin'    => true
]);

$factory->state(Role::class, 'country', [
    'admin'    => true
]);

$factory->state(Role::class, 'canton', [
     'admin'    => true
]);

$factory->state(Role::class, 'local', []);
