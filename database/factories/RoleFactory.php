<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Role;
use Faker\Generator as Faker;

$factory->define(Role::class, function (Faker $faker) {
    return [
        'group_id' => 1,
        'user_id'  => 1,
        'added_by' => 1,
        'admin'    => false
    ];
});
