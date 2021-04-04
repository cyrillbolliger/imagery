<?php


namespace App\Logo;


class GrueneVerts extends AbstractFlowerLogo
{
    private const BASE_LOGO_NAME = 'gruene-verts-%s.svg';
    private const REFERENCE_LOGO_NAME = 'gruene-verts.svg';

    public function getLogoIdentifier(int $width): string
    {
        return sprintf(self::BASE_LOGO_NAME, $this->colorScheme)
               ."-{$this->getSublineText()}-{$width}";
    }

    protected function getRelSublineOffsetX(): float
    {
        return 29.6;
    }

    protected function getRelSublineOffsetY(): float
    {
        return 102.1375;
    }

    protected function getAbsBaseLogoPath(): string
    {
        return $this->baseLogoDirPath
               .DIRECTORY_SEPARATOR
               .sprintf(self::BASE_LOGO_NAME, $this->colorScheme);
    }

    protected function getSublineFontSize(): float
    {
        return 5.38;
    }

    protected function getTestOverlayPath(): ?string
    {
        return $this->getReferenceLogoDir()
               .DIRECTORY_SEPARATOR
               .self::REFERENCE_LOGO_NAME;
    }
}
