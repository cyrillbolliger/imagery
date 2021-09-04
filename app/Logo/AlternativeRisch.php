<?php


namespace App\Logo;


use JetBrains\PhpStorm\Pure;

class AlternativeRisch extends SimpleLogoCompositor
{
    private const BASE_LOGO_NAME = 'alternative-risch-%s.svg';

    #[Pure] public function getLogoIdentifier(int $width): string
    {
        return sprintf(self::BASE_LOGO_NAME, $this->colorScheme)
               ."-{$width}";
    }

    #[Pure] protected function getAbsLogoPath(): string
    {
        return $this->baseLogoDirPath
            . DIRECTORY_SEPARATOR
            . sprintf(self::BASE_LOGO_NAME, $this->colorScheme);
    }
}
