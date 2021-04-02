<?php


namespace App\Logo;


use App\Exceptions\LogoException;
use Illuminate\Support\Str;
use Imagick;
use ImagickDraw;
use ImagickException;
use ImagickPixel;

abstract class AbstractLogo
{
    public const LOGO_COLOR_DARK = 'dark';
    public const LOGO_COLOR_LIGHT = 'light';
    public const LOGO_SUBLINE_MAX_CHAR = 80;

    protected const ROTATION_ANGLE = -5; // degrees

    protected const SUBLINE_BG_COLOR = 'rgb(132,180,20)';
    protected const SUBLINE_TEXT_COLOR = '#ffffff';
    protected const SUBLINE_FONT_NAME = 'SanukOT-Bold.otf';
    protected const SUBLINE_KERNING = -2.5;
    protected const SUBLINE_PADDINGS = [
        'top'    => 0.25,
        'left'   => 0.25,
        'bottom' => 0.25,
        'right'  => 0.25
    ];
    /**
     * The only purpose of this factor is to have nice numbers
     * for the getSublineFontSize() method. If you change the
     * factor, you must adapt all implementations of
     * getSublineFontSize().
     */
    protected const SUBLINE_FONT_SIZE_FACTOR = 100;

    protected string $colorScheme;
    protected string $sublineText;

    /**
     * @var Imagick[]
     */
    private array $logo;

    /**
     * AbstractLogo constructor.
     *
     * @param  string  $colorScheme  accepted values are:
     *                              - self::LOGO_COLOR_DARK
     *                              - self::LOGO_COLOR_LIGHT
     * @param  string  $sublineText
     * @throws LogoException
     */
    public function __construct(string $colorScheme, string $sublineText)
    {
        if (!in_array($colorScheme, [self::LOGO_COLOR_DARK, self::LOGO_COLOR_LIGHT])) {
            throw new LogoException("Invalid logo color scheme: $colorScheme");
        }

        if (strlen($sublineText) > self::LOGO_SUBLINE_MAX_CHAR) {
            throw new LogoException("Logo subline exceeds limit of ".self::LOGO_SUBLINE_MAX_CHAR." chars: $sublineText");
        }

        $this->colorScheme = $colorScheme;
        $this->sublineText = $sublineText;
    }

    /**
     * Get logo as TIFF encoded in CMYK
     *
     * @param  int  $width  width of logo before rotation (thus, the width of
     *                      the final logo is slightly bigger)
     * @return string
     * @throws LogoException
     */
    public function getTiff(int $width): string
    {
        $path = $this->getFinalFilePath('tiff', $width);

        if (file_exists($path)) {
            return $path;
        }

        $im = $this->getLogo($width);
        $im->setImageFormat('tiff');
        $im->transformImageColorspace(Imagick::COLORSPACE_CMYK);
        $im->setColorspace(Imagick::COLORSPACE_CMYK);

        $im->writeImage($path);

        return $path;
    }


    /**
     * Get logo as PNG encoded in sRGB
     *
     * @param  int  $width  width of logo before rotation (thus, the width of
     *                      the final logo is slightly bigger)
     * @return string
     * @throws LogoException
     */
    public function getPng(int $width): string
    {
        $path = $this->getFinalFilePath('png', $width);

        if (file_exists($path)) {
            return $path;
        }

        $im = $this->getLogo($width);
        $im->setImageFormat('png');
        $im->transformImageColorspace(Imagick::COLORSPACE_SRGB);
        $im->setColorspace(Imagick::COLORSPACE_SRGB);

        $im->writeImage($path);

        return $path;
    }

    /**
     * Get logo from class cache or create it if not in cache.
     *
     * @param  int  $width  width of logo before rotation (thus, the width of
     *                       the final logo is slightly bigger)
     * @return Imagick
     * @throws LogoException
     */
    private function getLogo(int $width): Imagick
    {
        if (!$this->logo[$width]) {
            $this->compose($width);
        }

        return clone $this->logo[$width];
    }

    abstract protected function getSublineOffsetX(): float;

    abstract protected function getSublineOffsetY(): float;

    /**
     * Create the logo as composition of the base logo and the subline.
     *
     * @param  int  $width  width of logo before rotation (thus, the width of
     * the final logo is slightly bigger)
     * @throws LogoException
     */
    protected function compose(int $width): void
    {
        $baseLogoIm = $this->getBaseLogoIm($width);
        $sublineIm  = $this->getSublineIm($width);

        $baseLogoWidth = $baseLogoIm->getImageWidth();
        $sublineWidth  = $this->getSublineOffsetY() + $sublineIm->getImageWidth();

        // if the subline exceeds the base logo we must redraw the base logo
        // and the subline with an adjusted width, so the final width matches
        // the expected width.
        if ($sublineWidth > $baseLogoWidth) {
            $adjustedWidth = ($width / $sublineWidth) * $width;

            $baseLogoIm = $this->getBaseLogoIm($adjustedWidth);
            $sublineIm  = $this->getSublineIm($adjustedWidth);

            $baseLogoWidth = $baseLogoIm->getImageWidth();
            $sublineWidth  = $this->getSublineOffsetY() + $sublineIm->getImageWidth();
        }

        $logoHeight = $this->getSublineOffsetY() + $sublineIm->getImageHeight();
        $logoWidth  = max($baseLogoWidth, $sublineWidth);

        // get the smallest possible container so that the logo will always fit
        // regardless of any rotation: longest side of the logo * sqrt(2)
        $longestSide = max($logoHeight, $logoWidth);
        $canvasSide  = $longestSide * sqrt(2);

        $canvas = new Imagick();
        $canvas->newImage($canvasSide, $canvasSide, 'transparent');

        // choose base offsets so the logo is placed in the center
        $baseX = ($canvasSide - $logoWidth) / 2;
        $baseY = ($canvasSide - $logoHeight) / 2;

        // add base logo and subline
        $canvas->compositeImage(
            $baseLogoIm,
            Imagick::COMPOSITE_DEFAULT,
            $baseX,
            $baseY
        );
        $canvas->compositeImage(
            $sublineIm,
            Imagick::COMPOSITE_DEFAULT,
            $baseX + $this->getSublineOffsetX(),
            $baseY + $this->getSublineOffsetY()
        );

        $canvas->rotateImage('transparent', self::ROTATION_ANGLE);
        $canvas->trimImage(0);

        $this->logo[$width] = $canvas;
    }

    /**
     * Get an image magic object with the base logo
     *
     * @param  int  $width
     * @return Imagick
     * @throws LogoException
     */
    protected function getBaseLogoIm(int $width): Imagick
    {
        $path = $this->getAbsBaseLogoPath();

        if (!file_exists($path)) {
            throw new LogoException('Base logo not found: '.$path);
        }

        $im = new Imagick();
        $im->setBackgroundColor('transparent');

        try {
            // read image to determine and set pixel density so the svg will be
            // rastered to the correct size (when loading it the second time).
            $im->readImage($path);
            $im->trimImage(0);
            $initialResolution = $im->getImageResolution();
            $resolutionRatio   = $initialResolution['x'] / $im->getImageWidth();
            $targetResolution  = $resolutionRatio * $width;
            $im->removeImage();
            $im->setResolution($targetResolution, $targetResolution);

            // reload the image, now with the correct pixel density
            $im->readImage($path);
        } catch (ImagickException $e) {
            throw new LogoException("Image magick can't read base logo: $e");
        }

        // cut borders
        $im->trimImage(0);

        try {
            $im->scaleImage($width, 0);
        } catch (ImagickException $e) {
            throw new LogoException("Image magick can't scale base logo: $e");
        }

        return $im;
    }

    abstract protected function getAbsBaseLogoPath(): string;

    protected function getSublineIm(int $baseLogoWidth): Imagick
    {
        $fontSize = $this->getSublineFontSize() * $baseLogoWidth / self::SUBLINE_FONT_SIZE_FACTOR;
        $text     = $this->getSublineText();

        $bgColor   = new ImagickPixel(self::SUBLINE_BG_COLOR);
        $fontColor = new ImagickPixel(self::SUBLINE_TEXT_COLOR);

        $im = new Imagick();

        // get text dimensions
        $draw = new ImagickDraw();
        $draw->setFontSize($fontSize);
        $draw->setFont(self::getSublineFontPath());
        $draw->setTextKerning(self::SUBLINE_KERNING);
        $textDims = $im->queryFontMetrics($draw, $text);

        // the fonts padding
        $padding = [
            'top'    => $fontSize * self::SUBLINE_PADDINGS['top'],
            'right'  => $fontSize * self::SUBLINE_PADDINGS['right'],
            'bottom' => $fontSize * self::SUBLINE_PADDINGS['bottom'],
            'left'   => $fontSize * self::SUBLINE_PADDINGS['left'],
        ];

        $height = $textDims['textWidth'] + $padding['top'] + $padding['bottom'];
        $width  = $textDims['textHeight'] + $padding['left'] + $padding['right'];

        // draw background
        $im->newImage($width, $height, $bgColor);

        // add text
        $draw->setFillColor($fontColor);
        $im->annotateImage(
            $draw,
            $padding['left'],
            $padding['top'],
            0,
            $text
        );

        return $im;
    }

    abstract protected function getSublineFontSize(): float;

    abstract protected function getSublineText(): string;

    private static function getStorageDir(): string
    {
        return create_dir(disk_path(config('app.logo_cache_dir')));
    }

    private function getFinalFilePath(string $ext, int $width): string
    {
        $fileBaseName = "{$this->getSublineText()}-{$this->colorScheme}-{$width}";
        $hash         = substr(hash('sha256', $fileBaseName), 0, 32);
        $slug         = Str::slug($fileBaseName);
        $filename     = "$slug-$hash.$ext";

        return self::getStorageDir().DIRECTORY_SEPARATOR.$filename;
    }

    protected static function getSublineFontPath(): string
    {
        return disk_path(
            config('app.protected_fonts_dir')
               .DIRECTORY_SEPARATOR
               .self::SUBLINE_FONT_NAME
        );
    }

    final protected static function getBaseLogoDir(): string
    {
        return disk_path(config('app.base_logo_dir'));
    }
}
