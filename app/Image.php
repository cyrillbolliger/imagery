<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Image extends Model
{
    use SoftDeletes;

    private const THUMB_MAX_WIDTH = 600;
    private const THUMB_MAX_HEIGHT = 5000;

    private const PATH_FULL = 'full';
    private const PATH_THUMB = 'thumb';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getImageStorageDir()
    {
        return self::getBaseDir().DIRECTORY_SEPARATOR.self::PATH_FULL;
    }

    public static function getThumbnailStorageDir()
    {
        return self::getBaseDir().DIRECTORY_SEPARATOR.self::PATH_THUMB;
    }

    private static function getBaseDir()
    {
        return config('app.image_dir');
    }

    public static function getPathFromStorageDir(string $storageDir)
    {
        return storage_path('app'.DIRECTORY_SEPARATOR.$storageDir);
    }

    /**
     * @param  string  $sourceImagePath
     * @param  string  $thumbnailPath
     *
     * @throws \ImagickException
     */
    public static function generateThumbnail(string $sourceImagePath, string $thumbnailPath)
    {
        $image = new \Imagick(realpath($sourceImagePath));

        $image->thumbnailImage(self::THUMB_MAX_WIDTH, self::THUMB_MAX_HEIGHT, true);
        $image->writeImage($thumbnailPath);
        $image->destroy();
    }
}
