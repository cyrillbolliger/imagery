<?php


namespace App\Logo;


use App\Exceptions\LogoException;
use Imagick;
use ImagickDraw;
use ImagickException;
use ImagickPixel;
use JetBrains\PhpStorm\Pure;

abstract class AbstractFlowerLogo implements LogoCompositor
{
    public const SUBLINE_MAX_CHAR = 80;
    protected const ROTATION_ANGLE = -5; // degrees

    protected const SUBLINE_BG_COLOR = '#E10078';
    protected const SUBLINE_TEXT_COLOR = '#ffffff';
    protected const SUBLINE_FONT_NAME = 'SanukOT-Bold.otf';
    protected const SUBLINE_KERNING = -5;
    protected const SUBLINE_PADDINGS = [
        'top'    => -0.1,
        'left'   => 0.1,
        'bottom' => -0.1,
        'right'  => 0.1
    ];
    /**
     * The only purpose of this factor is to have nice numbers
     * for the getSublineFontSize() method. If you change the
     * factor, you must adapt all implementations of
     * getSublineFontSize().
     */
    protected const SUBLINE_FONT_SIZE_FACTOR = 100;
    protected const SUBLINE_OFFSET_FACTOR = 100;

    protected const INITIAL_BASE_LOGO_RESOLUTION = 100;

    protected string $sublineText;

    private Imagick $baseLogoIm;
    private Imagick $sublineIm;
    protected string $colorScheme;
    protected string $baseLogoDirPath;

    /**
     * @param  string  $sublineText
     * @throws LogoException
     */
    public function __construct(string $sublineText)
    {
        if (strlen($sublineText) > self::SUBLINE_MAX_CHAR) {
            throw new LogoException("Logo subline exceeds limit of ".self::SUBLINE_MAX_CHAR." chars: $sublineText");
        }

        $this->sublineText = $sublineText;
    }

    public function compose(int $width): Imagick
    {
        $this->makeBaseLogoIm($width);
        $this->makeSublineIm($width);

        // if the subline exceeds the base logo we must redraw the base logo
        // and the subline with an adjusted width, so the final width matches
        // the expected width.
        if ($this->getSublineWidth() > $this->getBaseLogoWidth()) {
            $adjustedWidth = ($width / $this->getSublineWidth()) * $width;

            $this->makeBaseLogoIm($adjustedWidth);
            $this->makeSublineIm($adjustedWidth);
        }

        $logoHeight = $this->getRelSublineOffsetY() + $this->getSublineHeight();
        $logoWidth  = max($this->getBaseLogoWidth(), $this->getSublineWidth());

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
            $this->baseLogoIm,
            Imagick::COMPOSITE_DEFAULT,
            $baseX,
            $baseY
        );
        $canvas->compositeImage(
            $this->sublineIm,
            Imagick::COMPOSITE_DEFAULT,
            $baseX + $this->getAbsSublineX(),
            $baseY + $this->getAbsSublineY()
        );

        $canvas->rotateImage('transparent', self::ROTATION_ANGLE);
        $canvas->trimImage(0);

        return $canvas;
    }

    public function setColorScheme(string $colorScheme): void
    {
        $this->colorScheme = $colorScheme;
    }

    public function setBaseLogoDirPath(string $path): void
    {
        $this->baseLogoDirPath = $path;
    }

    /**
     * Get an image magic object with the base logo
     *
     * @param  int  $width
     * @throws LogoException
     */
    private function makeBaseLogoIm(int $width): void
    {
        $path = $this->getAbsBaseLogoPath();

        if (!file_exists($path)) {
            throw new LogoException('Base logo not found: '.$path);
        }

        $im = new Imagick();
        $im->setBackgroundColor('transparent');
        $im->setResolution(
            self::INITIAL_BASE_LOGO_RESOLUTION,
            self::INITIAL_BASE_LOGO_RESOLUTION
        );

        try {
            // read image to determine and set pixel density so the svg will be
            // rastered to the correct size (when loading it the second time).
            $im->readImage($path);
            $im->trimImage(0);
            $resolutionRatio  = self::INITIAL_BASE_LOGO_RESOLUTION / $im->getImageWidth();
            $targetResolution = $resolutionRatio * $width;
            $im->removeImage();
            $im->setResolution($targetResolution, $targetResolution);

            // reload the image, now with the correct pixel density
            $im->readImage($path);
        } catch (ImagickException $e) {
            throw new LogoException("Image magick can't read base logo: $e");
        }

        // cut borders
        $im->trimImage(0);

        $this->baseLogoIm = $im;
    }

    private function makeSublineIm(int $baseLogoWidth): void
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

        $height = $textDims['textHeight'] + $padding['top'] + $padding['bottom'];
        $width  = $textDims['textWidth'] + $padding['left'] + $padding['right'];

        // draw background
        $im->newImage($width, $height, $bgColor);

        // add text
        $draw->setFillColor($fontColor);
        $draw->setGravity(Imagick::GRAVITY_NORTHWEST);
        $im->annotateImage(
            $draw,
            $padding['left'],
            $padding['top'],
            0,
            $text
        );

        $this->sublineIm = $im;
    }

    private static function getSublineFontPath(): string
    {
        return disk_path(
            config('app.protected_fonts_dir')
            .DIRECTORY_SEPARATOR
            .self::SUBLINE_FONT_NAME
        );
    }

    #[Pure] private function getBaseLogoWidth(): int
    {
        return $this->baseLogoIm->getImageWidth();
    }

    #[Pure] private function getBaseLogoHeight(): int
    {
        return $this->baseLogoIm->getImageHeight();
    }

    private function getSublineWidth(): int
    {
        return ceil($this->getAbsSublineX()) + $this->sublineIm->getImageWidth();
    }

    private function getAbsSublineX(): float {
        return ($this->getRelSublineOffsetX() / self::SUBLINE_OFFSET_FACTOR)
                    * $this->getBaseLogoWidth();
    }

    private function getAbsSublineY(): float
    {
        return ($this->getRelSublineOffsetY() / self::SUBLINE_OFFSET_FACTOR)
               * $this->getBaseLogoHeight();
    }

    #[Pure] private function getSublineHeight(): int {
        return $this->sublineIm->getImageHeight();
    }

    abstract protected function getRelSublineOffsetX(): float;

    abstract protected function getRelSublineOffsetY(): float;

    abstract protected function getAbsBaseLogoPath(): string;

    abstract protected function getSublineFontSize(): float;

    abstract protected function getSublineText(): string;
}
