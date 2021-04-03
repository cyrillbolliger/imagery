<?php


namespace App\Logo;


use App\Exceptions\LogoException;
use Imagick;

interface LogoCompositor
{
    /**
     * Return the logo as image magick object of the given width
     *
     * @param  int  $width
     * @return Imagick
     *
     * @throws LogoException
     */
    public function compose(int $width): Imagick;

    public function setColorScheme(string $colorScheme): void;

    public function setBaseLogoDirPath(string $path): void;

    public function getLogoIdentifier(int $width): string;
}
