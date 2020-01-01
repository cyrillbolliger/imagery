<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Group;
use App\GroupLogo;
use App\Logo;
use Faker\Generator as Faker;

$factory->define(GroupLogo::class, function (Faker $faker) {
    return [
        'logo_id'  => Logo::first() ? Logo::first()->id : factory(Logo::class)->create()->id,
        'group_id' => Group::first() ? Group::first()->id : factory(Group::class)->create()->id,
    ];
});
