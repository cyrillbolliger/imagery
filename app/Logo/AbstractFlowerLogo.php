<?php


namespace App\Logo;


use App\Exceptions\LogoException;
use Imagick;
use ImagickDraw;
use ImagickPixel;
use JetBrains\PhpStorm\Pure;

abstract class AbstractFlowerLogo implements LogoCompositor
{
    use VectorImageLoaderTrait;

    public const SUBLINE_MAX_CHAR = 80;
    protected const ROTATION_ANGLE = -5; // degrees

    protected const SUBLINE_BG_COLOR = '#E10078';
    protected const SUBLINE_TEXT_COLOR = '#ffffff';
    protected const SUBLINE_FONT_NAME = 'SanukOT-Bold.otf';
    protected const SUBLINE_KERNING = -1.6;
    protected const KERNING_FACTOR = 100;
    protected const SUBLINE_PADDINGS = [
        'top'    => -0.07,
        'left'   => 0.13,
        'bottom' => -0.07,
        'right'  => 0.13
    ];
    /**
     * The only purpose of this factor is to have nice numbers
     * for the getSublineFontSize() method. If you change the
     * factor, you must adapt all implementations of
     * getSublineFontSize().
     */
    protected const SUBLINE_FONT_SIZE_FACTOR = 100;
    protected const SUBLINE_OFFSET_FACTOR = 100;

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
        $malsizedIm    = $this->composeInternal($width);
        $adjustedWidth = ($width / $malsizedIm->getImageWidth()) * $width;
        unset($malsizedIm);

        $im = $this->composeInternal(floor($adjustedWidth));

        $this->addTestOverlay($im, $width);

        return $im;
    }

    /**
     * Actually composes the logo from the base logo and the subline. As the
     * final width depends on the base logo's shape and the subline's length due
     * to the rotation, the final width cannot be specified directly but must be
     * measured from the generated logo. If you need the logo in a certain width
     * generate it, measure it's actual size then regenerate it with an adapted
     * $unrotated width.
     *
     * @param  int  $unrotatedWidth
     * @return Imagick
     * @throws LogoException
     */
    private function composeInternal(int $unrotatedWidth): Imagick
    {
        $this->makeBaseLogoIm($unrotatedWidth);
        $this->makeSublineIm($unrotatedWidth);

        // if the subline exceeds the base logo we must redraw the base logo
        // and the subline with an adjusted width, so the final width matches
        // the expected width.
        if ($this->getSublineWidth() > $this->getBaseLogoWidth()) {
            $adjustedWidth = ($unrotatedWidth / $this->getSublineWidth()) * $unrotatedWidth;

            $this->makeBaseLogoIm($adjustedWidth);
            $this->makeSublineIm($adjustedWidth);
        }

        $logoHeight = $this->getAbsSublineY() + $this->getSublineHeight();
        $logoWidth  = max($this->getBaseLogoWidth(), $this->getSublineWidth());

        $canvas = new Imagick();
        $canvas->newImage($logoWidth, $logoHeight, 'transparent');

        // add base logo and subline
        $canvas->compositeImage(
            $this->baseLogoIm,
            Imagick::COMPOSITE_DEFAULT,
            0,
            0
        );
        $canvas->compositeImage(
            $this->sublineIm,
            Imagick::COMPOSITE_DEFAULT,
            $this->getAbsSublineX(),
            $this->getAbsSublineY()
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

        $this->baseLogoIm = $this->imFromVectorFile($path, $width);
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
        $draw->setTextKerning($fontSize * self::SUBLINE_KERNING / self::KERNING_FACTOR);
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

    private function getAbsSublineX(): float
    {
        return ($this->getRelSublineOffsetX() / self::SUBLINE_OFFSET_FACTOR)
               * $this->getBaseLogoWidth();
    }

    private function getAbsSublineY(): float
    {
        return ($this->getRelSublineOffsetY() / self::SUBLINE_OFFSET_FACTOR)
               * $this->getBaseLogoHeight();
    }

    #[Pure] private function getSublineHeight(): int
    {
        return $this->sublineIm->getImageHeight();
    }

    /**
     * Horizontal offset of the subline in percent of the base logo's width.
     *
     * 30 is a good starting point
     *
     * @return float
     */
    abstract protected function getRelSublineOffsetX(): float;

    /**
     * Vertical offset of the subline in percent of the base logo's height.
     *
     * 100 is a good starting point.
     *
     * @return float
     */
    abstract protected function getRelSublineOffsetY(): float;

    /**
     * Path to the base logo.
     *
     * The class properties $this->baseLogoDirPath and $this->colorScheme are
     * your friends.
     *
     * @return string
     */
    abstract protected function getAbsBaseLogoPath(): string;

    /**
     * The font size of the subline.
     *
     * 10 is a good starting point.
     *
     * @return float
     */
    abstract protected function getSublineFontSize(): float;

    /**
     * Path to the reference logo.
     *
     * For testing purposes only.
     *
     * @return string|null
     */
    abstract protected function getTestOverlayPath(): ?string;

    /**
     * Mask the generated logo with the reference logo specified in
     * self::getTestOverlayPath(), so we can easily see the differences.
     * Use the APP_LOGO_OVERLAY environment variable to add the overlay.
     *
     * @param  Imagick  $canvas
     * @param  int  $width
     * @throws LogoException
     */
    private function addTestOverlay(Imagick $canvas, int $width): void
    {
        if (!config('app.logo_debug_overlay')) {
            return;
        }

        if (null === $this->getTestOverlayPath()) {
            return;
        }

        $overlay = $this->imFromVectorFile($this->getTestOverlayPath(), $width);
        $overlay->blackThresholdImage("#FFFFFF");

        $canvas->compositeImage(
            $overlay,
            Imagick::COMPOSITE_DIFFERENCE,
            0,
            0
        );
    }

    protected function getReferenceLogoDir(): string
    {
        return disk_path(config('app.reference_logo_dir'));
    }

    protected function getSublineText(): string
    {
        return mb_strtoupper($this->sublineText);
    }
}
