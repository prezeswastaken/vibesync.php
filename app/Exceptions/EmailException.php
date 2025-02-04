<?php

namespace App\Exceptions;

class EmailException extends AppException
{
    public static function userAlreadyHasEmail(): self
    {
        return new self('User already has email', 400);
    }
}
