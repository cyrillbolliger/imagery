<?php


namespace App\Logo;


use App\Exceptions\LogoException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Imagick;

class Logo
{
    public const LOGO_COLOR_DARK = 'dark';
    public const LOGO_COLOR_LIGHT = 'light';

    protected const MAX_LOGO_WIDTH = 5000;

    protected string $colorScheme;
    protected LogoCompositor $compositor;

    /**
     * @var Imagick[]
     */
    private array $logo = [];

    /**
     * @param  LogoCompositor  $compositor
     * @param  string  $colorScheme  accepted values are:
     *                              - self::LOGO_COLOR_DARK
     *                              - self::LOGO_COLOR_LIGHT
     * @throws LogoException
     */
    public function __construct(LogoCompositor $compositor, string $colorScheme)
    {
        self::validateColorScheme($colorScheme);

        $compositor->setColorScheme($colorScheme);
        $compositor->setBaseLogoDirPath(self::getBaseLogoDir());

        $this->colorScheme = $colorScheme;
        $this->compositor  = $compositor;
    }

    final public static function validateColorScheme(string $colorScheme)
    {
        if (!in_array($colorScheme, [self::LOGO_COLOR_DARK, self::LOGO_COLOR_LIGHT])) {
            throw new LogoException("Invalid logo color scheme: $colorScheme");
        }
    }

    final public static function getBaseLogoDir(): string
    {
        return config('app.base_logo_dir');
    }

    /**
     * Get logo as TIFF encoded in CMYK
     *
     * @param  int  $width  width of logo before rotation (thus, the width of
     *                      the final logo is slightly bigger)
     * @param  bool  $forceRecreate  refresh cached file
     * @return string
     * @throws LogoException
     */
    public function getTiff(int $width, bool $forceRecreate = false): string
    {
        $path = $this->getFinalFilePath('tiff', $width);

        if ($this->serveCached($forceRecreate, $path)) {
            return $path;
        }

        $im = $this->getCachedLogoIm($width);
        $im->setImageFormat('tiff');
        $im->transformImageColorspace(Imagick::COLORSPACE_CMYK);
        $im->setColorspace(Imagick::COLORSPACE_CMYK);

        $im->writeImage($path);

        return $path;
    }

    private function serveCached(bool $forceRecreate, string $path)
    {
        return !$forceRecreate
               && !config('app.logo_debug_overlay')
               && Storage::exists($path);
    }

    private function getFinalFilePath(string $ext, int $width): string
    {
        $idString = $this->compositor->getLogoIdentifier($width);
        $slug = Str::slug($idString);
        $hash = substr(hash('sha256', $slug), 0,16);

        if (config('app.logo_debug_overlay')) {
            $slug = 'debug-'.$slug;
        }

        // use slug and hash, because the slug could easily have a name conflict
        $filename = "$slug-$hash.$ext";

        return self::getStorageDirPath().DIRECTORY_SEPARATOR.$filename;
    }

    private static function getStorageDirPath(): string
    {
        return create_dir(config('app.logo_cache_dir'));
    }

    /**
     * Get logo from class cache or create it if not in cache.
     *
     * @param  int  $width  width of logo before rotation (thus, the width of
     *                       the final logo is slightly bigger)
     * @return Imagick
     * @throws LogoException
     */
    private function getCachedLogoIm(int $width): Imagick
    {
        if ($width > self::MAX_LOGO_WIDTH){
            throw new LogoException(
                sprintf(
                    'Max logo width is %dpx. Requested width: %d',
                    self::MAX_LOGO_WIDTH,
                    $width
                ),
                LogoException::OVERSIZE
            );
        }

        if (!array_key_exists($width, $this->logo)) {
            $this->logo[$width] = $this->compositor->compose($width);
        }

        return clone $this->logo[$width];
    }

    /**
     * Get logo as PNG encoded in sRGB
     *
     * @param  int  $width  width of logo before rotation (thus, the width of
     *                      the final logo is slightly bigger)
     * @param  bool  $forceRecreate  refresh cached file
     *
     * @return string
     * @throws LogoException
     */
    public function getPng(int $width, bool $forceRecreate = false): string
    {
        $path = $this->getFinalFilePath('png', $width);

        if ($this->serveCached($forceRecreate, $path)) {
            return $path;
        }

        $im = $this->getCachedLogoIm($width);
        $im->setImageFormat('png');
        $im->transformImageColorspace(Imagick::COLORSPACE_SRGB);
        $im->setColorspace(Imagick::COLORSPACE_SRGB);

        $im->writeImage(disk_path($path));

        return $path;
    }
}
