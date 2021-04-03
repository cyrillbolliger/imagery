<?php


namespace App\Logo;


class Gruene extends AbstractLogo
{
    private const BASE_LOGO_NAME = 'gruene-%s.svg';

    protected function getSublineOffsetX(): float
    {
        return 33.19;
    }

    protected function getSublineOffsetY(): float
    {
        return 102;
    }

    protected function getAbsBaseLogoPath(): string
    {
        return self::getBaseLogoDir()
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

    protected function getRotationAngle(): float
    {
        return parent::ROTATION_ANGLE;
    }
}
