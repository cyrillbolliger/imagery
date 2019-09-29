<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Logo;
use Faker\Generator as Faker;

$factory->define(Logo::class, function (Faker $faker) {
    return [
        'added_by' => 1,
        'filename' => '', // todo
        'name'     => $faker->word
    ];
});