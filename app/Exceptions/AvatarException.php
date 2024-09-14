<?php

namespace App\Exceptions;

class AvatarException extends AppException
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
