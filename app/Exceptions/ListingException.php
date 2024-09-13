<?php

namespace App\Exceptions;

class ListingException extends AppException
{
    public static function unauthorized(): self
    {
        return new self("You can't perform this action on this listing", 403);
    }

    public static function notFound(): self
    {
        return new self('Listing not found', 404);
    }
}
