<?php

declare(strict_types=1);

namespace Manguithre\FakerMadagascar\Data;

/**
 * Single source of truth for Malagasy mobile operator prefixes.
 *
 * Verified against the ARTEC national numbering plan (artec.mg),
 * the ITU/Wikipedia numbering tables and Airtel Madagascar's June 2026
 * announcement of the 035 prefix.
 */
final class PhonePrefixRepository
{
    /**
     * Mobile prefixes by operator.
     *
     * @var array<string, list<string>>
     */
    private const OPERATORS = [
        'orange' => ['032', '037'],
        'airtel' => ['033', '035'],
        'telma' => ['034', '038'],
        'bip' => ['039'],
    ];

    /**
     * All valid mobile prefixes, sorted.
     *
     * @return list<string>
     */
    public static function all(): array
    {
        $prefixes = [];

        foreach (self::OPERATORS as $operatorPrefixes) {
            foreach ($operatorPrefixes as $prefix) {
                $prefixes[] = $prefix;
            }
        }

        sort($prefixes);

        return $prefixes;
    }

    /**
     * Prefixes belonging to a given operator (e.g. 'orange', 'airtel', 'telma', 'bip').
     *
     * @return list<string>
     */
    public static function forOperator(string $operator): array
    {
        return self::OPERATORS[strtolower($operator)] ?? [];
    }

    /**
     * All operator names.
     *
     * @return list<string>
     */
    public static function operators(): array
    {
        return array_keys(self::OPERATORS);
    }

    /**
     * Whether the given 10-digit national number starts with a valid mobile prefix.
     */
    public static function isValidPrefix(string $nationalNumber): bool
    {
        return in_array(substr($nationalNumber, 0, 3), self::all(), true);
    }
}
