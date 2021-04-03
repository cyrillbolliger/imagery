<?php


namespace App\Logo;


use App\Exceptions\LogoException;
use Illuminate\Support\Str;
use Imagick;

class Logo
{
    public const LOGO_COLOR_DARK = 'dark';
    public const LOGO_COLOR_LIGHT = 'light';

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
        if (!in_array($colorScheme, [self::LOGO_COLOR_DARK, self::LOGO_COLOR_LIGHT])) {
            throw new LogoException("Invalid logo color scheme: $colorScheme");
        }

        $compositor->setColorScheme($colorScheme);
        $compositor->setBaseLogoDirPath(self::getBaseLogoDir());

        $this->colorScheme = $colorScheme;
        $this->compositor = $compositor;
    }

    final protected static function getBaseLogoDir(): string
    {
        return disk_path(config('app.base_logo_dir'));
    }

    /**
     * Get logo as TIFF encoded in CMYK
     *
     * @param  int  $width  width of logo before rotation (thus, the width of
     *                      the final logo is slightly bigger)
     * @param bool  $forceRecreate  refresh cached file
     * @return string
     * @throws LogoException
     */
    public function getTiff(int $width, bool $forceRecreate = false): string
    {
        $path = $this->getFinalFilePath('tiff', $width);

        if (!$forceRecreate && file_exists($path)) {
            return $path;
        }

        $im = $this->getCachedLogoIm($width);
        $im->setImageFormat('tiff');
        $im->transformImageColorspace(Imagick::COLORSPACE_CMYK);
        $im->setColorspace(Imagick::COLORSPACE_CMYK);

        $im->writeImage($path);

        return $path;
    }

    private function getFinalFilePath(string $ext, int $width): string
    {
        $slug         = Str::slug($this->compositor->getLogoIdentifier($width));
        $hash         = substr(hash('sha256', $slug), 0, 32);
        $filename     = "$slug-$hash.$ext";

        return self::getStorageDirPath().DIRECTORY_SEPARATOR.$filename;
    }

    private static function getStorageDirPath(): string
    {
        return create_dir(disk_path(config('app.logo_cache_dir')));
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
     * @param bool  $forceRecreate  refresh cached file
     *
     * @return string
     * @throws LogoException
     */
    public function getPng(int $width, bool $forceRecreate = false): string
    {
        $path = $this->getFinalFilePath('png', $width);

        if (!$forceRecreate && file_exists($path)) {
            return $path;
        }

        $im = $this->getCachedLogoIm($width);
        $im->setImageFormat('png');
        $im->transformImageColorspace(Imagick::COLORSPACE_SRGB);
        $im->setColorspace(Imagick::COLORSPACE_SRGB);

        $im->writeImage($path);

        return $path;
    }
}
