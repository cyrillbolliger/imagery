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
            return factory(Group::class)->create()->id;
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
    'added_by' => User::first() ? User::first()->id : 1,
    'admin'    => true
]);

$factory->state(Role::class, 'countryAdmin', [
    'group_id' => factory(GroupLogo::class)->state('country')->create()->group_id,
    'user_id'  => factory(User::class)->state('countryAdmin')->create(),
    'added_by' => User::first() ? User::first()->id : 1,
    'admin'    => true
]);

$factory->state(Role::class, 'cantonAdmin', [
    'group_id' => factory(GroupLogo::class)->state('canton')->create()->group_id,
    'user_id'  => factory(User::class)->state('cantonAdmin')->create(),
    'added_by' => User::first() ? User::first()->id : 1,
    'admin'    => true
]);

$factory->state(Role::class, 'localUser', [
    'group_id' => factory(GroupLogo::class)->state('local')->create()->group_id,
    'user_id'  => factory(User::class)->state('localUser')->create(),
    'added_by' => User::first() ? User::first()->id : 1,
    'admin'    => false
]);
