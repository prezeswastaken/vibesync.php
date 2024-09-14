<?php

namespace App\Exceptions;

class SocialiteException extends AppException
{
    public static function emailTaken(): self
    {
        return new self('Account with this email already exists. Please login with your email and password.', 409);
    }
}
