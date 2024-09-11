<?php

namespace App\Exceptions;

use Exception;

class AvatarException extends Exception
{
    public static function cantStore(): self
    {
        return new self("Can't store the avatar", 500);
    }

    public static function cantDelete(): self
    {
        return new self("Can't delete the old avatar", 500);
    }
}
