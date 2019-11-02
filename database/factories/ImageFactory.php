<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Image;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

$factory->define(Image::class, function (Faker $faker) {
    $imageWidth  = 1080;
    $imageHeight = 1080;

    $relDir = Image::getImageStorageDir();

    Storage::makeDirectory($relDir);

    $absDir = disk_path($relDir);

    $filename = $faker->image($absDir, $imageWidth, $imageHeight, 'cats', false);
    Storage::setVisibility($relDir.DIRECTORY_SEPARATOR.$filename, 'private');

    return [
        'user_id'  => function () {
            return factory(User::class)->create()->id;
        },
        'filename' => $filename,
        'width'    => $imageWidth,
        'height'   => $imageHeight
    ];
});
