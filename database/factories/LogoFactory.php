<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Logo;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Storage;

$factory->define(Logo::class, function (Faker $faker) {
    return [
        'added_by' => User::first() ? User::first()->id : 1,
        'name'     => $faker->city,
        'type'     => $faker->randomElement([
            'gruene',
            'verts',
            'verdi',
            'verda',
            'gruene-verts',
            'alternative'
        ]),
    ];
});
