<?php

namespace App\Exceptions;

class UpdateAccountException extends AppException
{
    public static function invalidPassword(): self
    {
        return new self('Invalid password', 400);
    }
}
