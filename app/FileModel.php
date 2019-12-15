<?php


namespace App;


interface FileModel
{
    public function getRelPath($arg = null);

    public function getRelThumbPath();
}
