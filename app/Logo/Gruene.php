<?php


namespace App\Logo;


class Gruene extends AbstractFlowerLogo
{
    private const BASE_LOGO_NAME = 'gruene-%s.svg';
    private const REFERENCE_LOGO_NAME = 'gruene.svg';

    protected function getRelSublineOffsetX(): float
    {
        return 33.19;
    }

    protected function getRelSublineOffsetY(): float
    {
        return 103;
    }

    protected function getAbsBaseLogoPath(): string
    {
        return $this->baseLogoDirPath
               .DIRECTORY_SEPARATOR
               .sprintf(self::BASE_LOGO_NAME, $this->colorScheme);
    }

    protected function getSublineFontSize(): float
    {
        return 7.6;
    }

    public function getLogoIdentifier(int $width): string
    {
        return sprintf(self::BASE_LOGO_NAME, $this->colorScheme)
               ."-{$this->getSublineText()}-{$width}";
    }

    protected function getTestOverlayPath(): ?string
    {
        return $this->getReferenceLogoDir()
               .DIRECTORY_SEPARATOR
               .self::REFERENCE_LOGO_NAME;
    }
}
