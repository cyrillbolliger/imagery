<?php


namespace App\Logo;


class GrueneVertes extends AbstractFlowerLogo
{
    private const BASE_LOGO_NAME = 'gruene-vert-e-s-%s.svg';
    private const REFERENCE_LOGO_NAME = 'gruene-vert-e-s.svg';

    public function getLogoIdentifier(int $width): string
    {
        return sprintf(self::BASE_LOGO_NAME, $this->colorScheme)
               ."-{$this->getSublineText()}-{$width}";
    }

    protected function getRelSublineOffsetX(): float
    {
        return 26;
    }

    protected function getRelSublineOffsetY(): float
    {
        return 102.125;
    }

    protected function getAbsBaseLogoPath(): string
    {
        return $this->baseLogoDirPath
               .DIRECTORY_SEPARATOR
               .sprintf(self::BASE_LOGO_NAME, $this->colorScheme);
    }

    protected function getSublineFontSize(): float
    {
        return 4.7725;
    }

    protected function getTestOverlayPath(): ?string
    {
        return $this->getReferenceLogoDir()
               .DIRECTORY_SEPARATOR
               .self::REFERENCE_LOGO_NAME;
    }
}
