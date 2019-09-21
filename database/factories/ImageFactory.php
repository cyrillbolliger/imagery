<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Image;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Storage;

$factory->define(Image::class, function (Faker $faker) {
    $imageWidth  = 640;
    $imageHeight = 480;

    $imageDirName = config('app.image_dir');
    Storage::disk('local')->makeDirectory($imageDirName);
    $imageDirPath = storage_path('app'.DIRECTORY_SEPARATOR.$imageDirName);
    $imageName    = $faker->image($imageDirPath, $imageWidth, $imageHeight, 'cats', false);

    return [
        'user_id'  => 1,
        'filename' => $imageName,
        'hash'     => md5_file($imageDirPath.DIRECTORY_SEPARATOR.$imageName),
        'width'    => $imageWidth,
        'height'   => $imageHeight
    ];
});
