<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Group;
use Faker\Generator as Faker;

$factory->define(Group::class, function (Faker $faker) {
    return [
        'parent_id' => 1,
        'added_by'  => 1,
        'name'      => $faker->word
    ];
});
