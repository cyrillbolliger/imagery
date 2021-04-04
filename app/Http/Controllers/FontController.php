<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FontController extends Controller
{
    public function show(string $font)
    {
        $fontModel = new class($font) implements \App\FileModel
        {
            private $font;

            public function __construct($font)
            {
                $this->font = $font;
            }

            public function getRelPath($arg = null): string
            {
                return config('app.protected_fonts_dir')
                       .DIRECTORY_SEPARATOR
                       .$this->font;
            }

            public function getRelThumbPath(): string
            {
            }
        };

        $fileController = new FileController();

        return $fileController->show($fontModel);
    }
}
