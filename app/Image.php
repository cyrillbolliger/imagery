<?php

namespace App;

use App\Exceptions\ThumbnailException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
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
 * @property Legal|null $legal
 * @property string $type
 * @property string $background
 * @property string $filename
 * @property int $width
 * @property int $height
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Image extends Model implements FileModel
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
        $dir = self::getBaseDir().DIRECTORY_SEPARATOR.self::PATH_FULL;

        return create_dir($dir);
    }

    public static function getThumbnailStorageDir()
    {
        $dir = self::getBaseDir().DIRECTORY_SEPARATOR.self::PATH_THUMB;

        return create_dir($dir);
    }

    private static function getBaseDir()
    {
        return trim(config('app.image_dir'), '/');
    }

    /**
     * Generate a thumbnail of this image
     *
     * @throws \ImagickException
     * @throws ThumbnailException
     */
    public function generateThumbnail()
    {
        $thumbPath = $this->getRelThumbPath();

        if (Storage::exists($thumbPath)) {
            return;
        }

        $imagePath = disk_path($this->getRelPath());
        $image     = new Imagick(realpath($imagePath));

        if ( ! (
            $image->thumbnailImage(self::THUMB_MAX_WIDTH, self::THUMB_MAX_HEIGHT, true)
            && $image->writeImage(disk_path($thumbPath))
            && $image->destroy()
        )) {
            throw new ThumbnailException('Thumbnail generation failed.');
        }

        Storage::setVisibility($thumbPath, 'private');
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
        return $query->join('legals', 'images.id', '=', 'legals.image_id')
                     ->select('images.*')
                     ->where('legals.shared', true);
    }

    public function getRelPath()
    {
        return self::getImageStorageDir().DIRECTORY_SEPARATOR.$this->filename;
    }

    public function getRelThumbPath()
    {
        return self::getThumbnailStorageDir().DIRECTORY_SEPARATOR.$this->filename;
    }
}
