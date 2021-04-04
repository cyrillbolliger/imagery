<?php


namespace App;


interface FileModel
{
    public function getRelPath($arg = null): string;

    public function getRelThumbPath(): string;
}
