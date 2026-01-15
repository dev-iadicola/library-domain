<?php

declare(strict_types=1);

namespace Iadicola\Domain\Helpers;

class Filters
{
    public static function arrayIntersectKey(array $data, array $fillable): array
    {
        return array_intersect_key(
            $data,
            array_flip($fillable)
        );
    }
}
