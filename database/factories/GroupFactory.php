<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Group;
use App\User;
use Faker\Generator as Faker;

$factory->define(Group::class, function (Faker $faker) {
    return [
        'parent_id' => Group::first() ? Group::first()->id : 1,
        'added_by'  => User::first() ? User::first()->id : 1,
        'name'      => $faker->word
    ];
});

$factory->state(Group::class, 'root', [
    'name'      => 'Root Group'
]);
