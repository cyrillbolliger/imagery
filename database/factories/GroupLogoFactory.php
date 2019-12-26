<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Group;
use App\GroupLogo;
use App\Logo;
use Faker\Generator as Faker;

$factory->define(GroupLogo::class, function (Faker $faker) {
    return [
        'logo_id'  => factory(Logo::class)->create()->id,
        'group_id' => factory(Group::class)->create()->id,
    ];
});
