<?php


namespace App\Logo;


use App\Exceptions\LogoException;

abstract class AbstractLogo
{
    public const LOGO_COLOR_DARK = 'dark';
    public const LOGO_COLOR_LIGHT = 'light';
    public const LOGO_SUBLINE_MAX_LEN = 80;
    public const FINAL_BASE_LOGO_WIDTH = 5000;

    protected const ROTATION_ANGLE = -5; // degrees

    protected const SUBLINE_BG_COLOR = 'rgb(132,180,20)';
    protected const SUBLINE_TEXT_COLOR = '#ffffff';
    protected const SUBLINE_FONT_PATH = ''; // todo
    protected const SUBLINE_KERNING = -2.5;
    protected const SUBLINE_PADDINGS = [
        'top'    => 0.25,
        'left'   => 0.25,
        'bottom' => 0.25,
        'right'  => 0.25
    ];

    protected string $colorScheme;
    protected string $sublineText;

    private \Imagick $logo;

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

        if (strlen($sublineText) > self::LOGO_SUBLINE_MAX_LEN) {
            throw new LogoException("Logo subline exceeds limit of ".self::LOGO_SUBLINE_MAX_LEN." chars: $sublineText");
        }

        $this->colorScheme = $colorScheme;
        $this->sublineText = $sublineText;
    }

    public function getTiff(): string
    {

    }

    abstract protected function getSublineOffsetX(): float;

    abstract protected function getSublineOffsetY(): float;

    protected function compose(): void
    {
        $baseLogoIm = $this->getBaseLogoIm();
        $sublineIm  = $this->getSublineIm();

        $logoHeight = $this->getSublineOffsetY() + $sublineIm->getImageHeight();
        $logoWidth  = max(
            $baseLogoIm->getImageWidth(),
            $this->getSublineOffsetY() + $sublineIm->getImageWidth()
        );

        // get the smallest possible container so that the logo will always fit
        // regardless of any rotation: longest side of the logo * sqrt(2)
        $longestSide = max($logoHeight, $logoWidth);
        $canvasSide  = $longestSide * sqrt(2);

        $canvas = new \Imagick();
        $canvas->newImage($canvasSide, $canvasSide, 'transparent');

        // choose base offsets so the logo is placed in the center
        $baseX = ($canvasSide - $logoWidth) / 2;
        $baseY = ($canvasSide - $logoHeight) / 2;

        // add base logo and subline
        $canvas->compositeImage(
            $baseLogoIm,
            \Imagick::COMPOSITE_DEFAULT,
            $baseX,
            $baseY
        );
        $canvas->compositeImage(
            $sublineIm,
            \Imagick::COMPOSITE_DEFAULT,
            $baseX + $this->getSublineOffsetX(),
            $baseY + $this->getSublineOffsetY()
        );

        $canvas->rotateImage('transparent', self::ROTATION_ANGLE);
        $canvas->trimImage(0);

        $this->logo = $canvas;
    }

    /**
     * Get an image magic object with the base logo
     *
     * @return \Imagick
     * @throws LogoException
     */
    protected function getBaseLogoIm(): \Imagick
    {
        $path = $this->getAbsBaseLogoPath();

        if (!file_exists($path)) {
            throw new LogoException('Base logo not found: '.$path);
        }

        $im = new \Imagick();
        $im->setBackgroundColor('transparent');

        try {
            // read image to determine and set pixel density so the svg will be
            // rastered to the correct size (when loading it the second time).
            $im->readImage($path);
            $initialResolution = $im->getImageResolution();
            $resolutionRatio = $initialResolution['x'] / $im->getImageWidth();
            $targetResolution = $resolutionRatio * self::FINAL_BASE_LOGO_WIDTH;
            $im->removeImage();
            $im->setResolution($targetResolution, $targetResolution);

            // reload the image, now with the correct pixel density
            $im->readImage($path);
        } catch (\ImagickException $e) {
            throw new LogoException("Image magick can't read base logo: $e");
        }

        // cut borders
        $im->trimImage(0);

        try {
            $im->scaleImage(self::FINAL_BASE_LOGO_WIDTH, 0);
        } catch (\ImagickException $e) {
            throw new LogoException("Image magick can't scale base logo: $e");
        }

        return $im;
    }

    abstract protected function getAbsBaseLogoPath(): string;

    protected function getSublineIm(): \Imagick
    {
        $fontSize = $this->getSublineFontSize();
        $text     = $this->getSublineText();

        $bgColor   = new \ImagickPixel(self::SUBLINE_BG_COLOR);
        $fontColor = new \ImagickPixel(self::SUBLINE_TEXT_COLOR);

        $im = new \Imagick();

        // get text dimensions
        $draw = new \ImagickDraw();
        $draw->setFontSize($fontSize);
        $draw->setFont(self::SUBLINE_FONT_PATH);
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
}
