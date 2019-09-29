<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\GroupLogo;
use Faker\Generator as Faker;

$factory->define(GroupLogo::class, function (Faker $faker) {
    return [
        'logo_id'  => 1,
        'group_id' => 1
    ];
});
