<?php


namespace App\Logo;


class Gruene extends AbstractFlowerLogo
{
    private const BASE_LOGO_NAME = 'gruene-%s.svg';

    protected function getRelSublineOffsetX(): float
    {
        return 33.19;
    }

    protected function getRelSublineOffsetY(): float
    {
        return 102;
    }

    protected function getAbsBaseLogoPath(): string
    {
        return $this->baseLogoDirPath
               .DIRECTORY_SEPARATOR
               .sprintf(self::BASE_LOGO_NAME, $this->colorScheme);
    }

    protected function getSublineFontSize(): float
    {
        return 8;
    }

    protected function getSublineText(): string
    {
        return mb_strtoupper($this->sublineText);
    }

    public function getLogoIdentifier(int $width): string
    {
        return sprintf(self::BASE_LOGO_NAME, $this->colorScheme)
               ."-{$this->getSublineText()}-{$width}";
    }
}
