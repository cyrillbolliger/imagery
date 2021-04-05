<?php


namespace App\Logo;


use App\Exceptions\LogoException;
use Imagick;

abstract class SimpleLogoCompositor implements LogoCompositor
{
    use VectorImageLoaderTrait;

    protected string $baseLogoDirPath;
    protected string $colorScheme;

    public function compose(int $width): Imagick
    {
        $path = $this->getAbsLogoPath();

        return $this->imFromVectorFile($path, $width);
    }

    /**
     * Path to the logo.
     *
     * The class properties $this->baseLogoDirPath and $this->colorScheme are
     * your friends.
     *
     * @return string
     */
    abstract protected function getAbsLogoPath(): string;

    public function setColorScheme(string $colorScheme): void
    {
        $this->colorScheme = $colorScheme;
    }

    public function setBaseLogoDirPath(string $path): void
    {
        $this->baseLogoDirPath = $path;
    }
}
