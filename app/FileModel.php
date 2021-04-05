<?php


namespace App;


interface FileModel
{
    public function getRelPath(array $args = []): string;

    public function getRelThumbPath(): string;
}
