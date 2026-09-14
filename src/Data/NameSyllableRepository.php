<?php

declare(strict_types=1);

namespace Manguithre\FakerMadagascar\Data;

class NameSyllableRepository
{
    private static ?array $cache = null;

    public static function all(): array
    {
        if (self::$cache === null) {
            $file = __DIR__ . '/../../resources/data/name-syllables.json';

            if (!file_exists($file)) {
                throw new \UnexpectedValueException("Name syllables data file not found: $file");
            }

            self::$cache = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
        }

        return self::$cache;
    }

    public static function prefixes(string $type): array
    {
        return self::section($type)['prefixes'] ?? [];
    }

    public static function middles(string $type): array
    {
        return self::section($type)['middles'] ?? [];
    }

    public static function suffixes(string $type): array
    {
        return self::section($type)['suffixes'] ?? [];
    }

    /**
     * The syllable section for a name type, throwing for unknown types.
     *
     * @return array<string, list<string>>
     */
    private static function section(string $type): array
    {
        $section = self::all()[$type] ?? null;

        if (! is_array($section)) {
            throw new \UnexpectedValueException("Unknown name type: $type");
        }

        return $section;
    }
}
