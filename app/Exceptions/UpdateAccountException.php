<?php

namespace App\Exceptions;

class UpdateAccountException extends AppException
{
    public static function invalidPassword(): self
    {
        return new self('Invalid password', 400);
    }

    public static function wrongCurrentEmail(): self
    {
        return new self('Invalid current email', 400);
    }
}
