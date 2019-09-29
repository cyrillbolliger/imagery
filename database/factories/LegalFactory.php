<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Legal;
use Faker\Generator as Faker;

$factory->define(Legal::class, function (Faker $faker) {

    $originator_type = $faker->randomElement([
        Legal::ORIGINATOR_USER,
        Legal::ORIGINATOR_STOCK,
        Legal::ORIGINATOR_AGENCY,
        Legal::ORIGINATOR_FRIEND,
        Legal::ORIGINATOR_UNKNOWN,
    ]);

    $personality = $faker->randomElement([
        Legal::PERSONALITY_NOT_APPLICABLE,
        Legal::PERSONALITY_AGREEMENT,
        Legal::PERSONALITY_PUBLIC_INTEREST,
        Legal::PERSONALITY_UNKNOWN,
        Legal::PERSONALITY_NO_AGREEMENT,
    ]);

    $licence = $faker->randomElement([
        Legal::LICENCE_CC,
        Legal::LICENCE_CC_ATTRIBUTION,
        Legal::LICENCE_OTHER,
    ]);

    $shared = false;
    if (in_array($originator_type, [
            Legal::ORIGINATOR_USER,
            Legal::ORIGINATOR_FRIEND
        ]) && in_array($personality, [
            Legal::PERSONALITY_NOT_APPLICABLE,
            Legal::PERSONALITY_AGREEMENT,
            Legal::PERSONALITY_PUBLIC_INTEREST
        ])) {
        // 80% shared
        $shared = $faker->numberBetween(0, 9) >= 2;
    }

    return [
        'image_id'             => 1,
        'right_of_personality' => $personality,
        'originator_type'      => $originator_type,
        'licence'              => $originator_type === Legal::ORIGINATOR_STOCK ? $licence : null,
        'originator'           => $faker->name,
        'stock_url'            => $originator_type === Legal::ORIGINATOR_STOCK ? $faker->url : null,
        'shared'               => $shared
    ];
});
