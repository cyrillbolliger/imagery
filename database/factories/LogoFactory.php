<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Logo;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Storage;

$factory->define(Logo::class, function (Faker $faker) {
    $dir  = Logo::getStorageDir();
    $path = disk_path($dir);
    Storage::makeDirectory($dir);
    $filename = $faker->image($path, 200, 200, 'technics', false);

    return [
        'added_by' => User::first() ? User::first()->id : 1,
        'filename' => $filename,
        'name'     => $faker->city
    ];
});
