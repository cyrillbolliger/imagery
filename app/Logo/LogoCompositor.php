<?php


namespace App\Logo;


use App\Exceptions\LogoException;
use Imagick;

interface LogoCompositor
{
    /**
     * Return the logo as image magick object of the given width.
     *
     * @param  int  $width
     * @return Imagick
     *
     * @throws LogoException
     */
    public function compose(int $width): Imagick;

    /**
     * Set the color scheme of the logo.
     *
     * @param  string  $colorScheme
     */
    public function setColorScheme(string $colorScheme): void;

    /**
     * Set the absolute path to the directory containing the base logo files.
     *
     * @param  string  $path
     */
    public function setBaseLogoDirPath(string $path): void;

    /**
     * Return a string identifying the logo, it's colorscheme and it's width.
     *
     * E.g.: "gruene-light.svg NIDWALDEN 5000"
     *
     * It is used for caching the logo file.
     *
     * @param  int  $width
     * @return string
     */
    public function getLogoIdentifier(int $width): string;
}
