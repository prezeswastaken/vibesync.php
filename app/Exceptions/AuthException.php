<?php

namespace App\Exceptions;

class AuthException extends AppException
{
    public static function unauthorized(): self
    {
        return new self('Unauthorized! Those credentials didn\'t match our records.', 401);
    }
}
