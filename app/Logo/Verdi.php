<?php


namespace App\Logo;


class Verdi extends AbstractFlowerLogo
{
    private const BASE_LOGO_NAME = 'verdi-%s.svg';
    private const REFERENCE_LOGO_NAME = 'verdi.svg';

    public function getLogoIdentifier(int $width): string
    {
        return sprintf(self::BASE_LOGO_NAME, $this->colorScheme)
               ."-{$this->getSublineText()}-{$width}";
    }

    protected function getRelSublineOffsetX(): float
    {
        return 32.1;
    }

    protected function getRelSublineOffsetY(): float
    {
        return 103.4;
    }

    protected function getAbsBaseLogoPath(): string
    {
        return $this->baseLogoDirPath
               .DIRECTORY_SEPARATOR
               .sprintf(self::BASE_LOGO_NAME, $this->colorScheme);
    }

    protected function getSublineFontSize(): float
    {
        return 7.1;
    }

    protected function getTestOverlayPath(): ?string
    {
        return null;
    }
}
