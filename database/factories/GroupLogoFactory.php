<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Group;
use App\GroupLogo;
use App\Logo;
use Faker\Generator as Faker;

$factory->define(GroupLogo::class, function (Faker $faker) {
    return [
        'logo_id'  => Logo::first() ? Logo::first()->id : 1,
        'group_id' => Group::first() ? Group::first()->id : 1,
    ];
});
