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

$factory->state(GroupLogo::class, 'country', [
    'logo_id'  => factory(Logo::class)->state('country')->create()->id,
    'group_id' => factory(Group::class)->state('country')->create()->id,
]);

$factory->state(GroupLogo::class, 'canton', [
    'logo_id'  => factory(Logo::class)->state('canton')->create()->id,
    'group_id' => factory(Group::class)->state('canton')->create()->id,
]);

$factory->state(GroupLogo::class, 'local', [
    'logo_id'  => factory(Logo::class)->state('local')->create()->id,
    'group_id' => factory(Group::class)->state('local')->create()->id,
]);
