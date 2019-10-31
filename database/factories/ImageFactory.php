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

    $tempDir  = 'tmp';
    $imageDir = Image::getImageStorageDir();
    $thumbDir = Image::getThumbnailStorageDir();

    Storage::makeDirectory($tempDir);
    Storage::makeDirectory($imageDir);
    Storage::makeDirectory($thumbDir);

    $tempDirPath = disk_path($tempDir);

    $tempImagePath = $faker->image($tempDirPath, $imageWidth, $imageHeight, 'cats', true);
    $imageRelPath  = Storage::putFile($imageDir, new File($tempImagePath), 'private');

    $imagePath            = disk_path($imageRelPath);
    $filename             = basename($imagePath);
    $thumbnailStoragePath = $thumbDir.DIRECTORY_SEPARATOR.$filename;
    $thumbnailPath        = disk_path($thumbnailStoragePath);

    Image::generateThumbnail($imagePath, $thumbnailPath);
    Storage::setVisibility($thumbnailStoragePath, 'private');

    Storage::deleteDirectory($tempDir);

    return [
        'user_id'  => function () {
            return factory(User::class)->create()->id;
        },
        'filename' => $filename,
        'width'    => $imageWidth,
        'height'   => $imageHeight
    ];
});
