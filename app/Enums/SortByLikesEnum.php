<?php

declare(strict_types=1);

namespace App\Enums;

use Illuminate\Http\Request;

enum SortByLikesEnum
{
    case Ascending;
    case Descending;
    case None;

    public static function fromRequest(Request $request): self
    {
        return match ($request->get('sortByLikes')) {
            'asc' => self::Ascending,
            'desc' => self::Descending,
            default => self::None,
        };
    }
}
