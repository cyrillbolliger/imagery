<?php

namespace App\Exceptions;

use Exception;

class UploadException extends Exception
{
    public const DATA_FORMAT = 1;
    public const BASE64 = 2;
    public const STORE = 3;
    public const PART = 4;
    public const SIZE = 5;
}
