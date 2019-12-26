<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Group;
use App\User;
use Faker\Generator as Faker;

$factory->define(Group::class, function (Faker $faker) {
    return [
        'parent_id' => Group::first(),
        'added_by'  => User::first() ? User::first()->id : 1,
        'name'      => $faker->word
    ];
});

$factory->state(Group::class, 'root', [
    'name'      => 'Root Group'
]);

$factory->state(Group::class, 'country', [
    'name'      => 'Country'
]);

$factory->state(Group::class, 'canton', [
    'name'      => 'Canton'
]);

$factory->state(Group::class, 'local', [
    'name'      => 'Local'
]);
