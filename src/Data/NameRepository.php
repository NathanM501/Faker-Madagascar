<?php

declare(strict_types=1);

namespace Manguithre\FakerMadagascar\Data;

class NameRepository
{
    private static ?array $firstNames = null;

    private static ?array $lastNames = null;

    public static function firstNames(): array
    {
        return self::$firstNames ??= self::load('first_names.json');
    }

    public static function lastNames(): array
    {
        return self::$lastNames ??= self::load('last_names.json');
    }

    private static function load(string $filename): array
    {
        $path = __DIR__ . '/../../resources/data/' . $filename;

        return json_decode(file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);
    }
}
