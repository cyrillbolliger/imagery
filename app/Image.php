<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Imagick;

/**
 * Class Image
 * @package App
 *
 * @property int $id
 * @property int $user_id
 * @property User|null $user
 * @property int|null $logo_id
 * @property Logo|null $logo
 * @property int|null $original_id
 * @property Image|null $original
 * @property Legal|null $shared
 * @property string $type
 * @property string $background
 * @property string $filename
 * @property int $width
 * @property int $height
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Image extends Model
{
    use SoftDeletes;

    private const THUMB_MAX_WIDTH = 600;
    private const THUMB_MAX_HEIGHT = 5000;

    private const PATH_FULL = 'full';
    private const PATH_THUMB = 'thumb';

    public const TYPE_RAW = 'raw';
    public const TYPE_FINAL = 'final';

    public const BG_GRADIENT = 'gradient';
    public const BG_TRANSPARENT = 'transparent';
    public const BG_CUSTOM = 'custom';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function legal()
    {
        return $this->hasOne(Legal::class);
    }

    public function logo()
    {
        return $this->belongsTo(Legal::class);
    }

    public function isFinal()
    {
        return $this->type === self::TYPE_FINAL;
    }

    public function isShareable()
    {
        return $this->legal && $this->legal->shared;
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

    /**
     * @param  string  $sourceImagePath
     * @param  string  $thumbnailPath
     *
     * @throws \ImagickException
     */
    public static function generateThumbnail(string $sourceImagePath, string $thumbnailPath)
    {
        $image = new Imagick(realpath($sourceImagePath));

        $image->thumbnailImage(self::THUMB_MAX_WIDTH, self::THUMB_MAX_HEIGHT, true);
        $image->writeImage($thumbnailPath);
        $image->destroy();
    }

    /**
     * Scope a query to only include raw images.
     *
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeRaw($query)
    {
        return $query->where('type', self::TYPE_RAW);
    }

    /**
     * Scope a query to only include final images.
     *
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeFinal($query)
    {
        return $query->where('type', self::TYPE_FINAL);
    }

    /**
     * Scope a query to only include shareable images.
     *
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeShareable($query)
    {
        return $query->join('legals', 'images.id', '=', 'legals.id')
                     ->select('images.*')
                     ->where('legals.shared', true);
    }
}
