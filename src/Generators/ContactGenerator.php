<?php

declare(strict_types=1);

namespace Manguithre\FakerMadagascar\Generators;

use Manguithre\FakerMadagascar\Data\PhonePrefixRepository;
use Manguithre\FakerMadagascar\Support\Config;

class ContactGenerator
{
    public function __construct() {}

    public function phoneNumber(?string $operator = null): string
    {
        $prefixes = $operator !== null
            ? PhonePrefixRepository::forOperator($operator)
            : PhonePrefixRepository::all();

        if ($prefixes === []) {
            throw new \InvalidArgumentException("Unknown operator: $operator");
        }

        $prefix = $prefixes[array_rand($prefixes)];
        $body = '';

        for ($i = 0; $i < 7; $i++) {
            $body .= random_int(0, 9);
        }

        return $prefix . $body;
    }

    public function cin(): string
    {
        $config = Config::get('cin_format', 'auto');

        $format = $config === 'auto' ? ['compact', 'separated'][random_int(0, 1)] : $config;

        $bureau = str_pad((string) random_int(1, 22), 2, '0', STR_PAD_LEFT);
        $year = str_pad((string) random_int(0, 99), 2, '0', STR_PAD_LEFT);
        $sequence = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        $cin = $bureau . $year . $sequence;

        return match ($format) {
            'separated' => substr($cin, 0, 2) . '-' . substr($cin, 2, 2) . '-' . substr($cin, 4, 4),
            'compact' => $cin,
            default => throw new \InvalidArgumentException("Invalid cin_format config: $format"),
        };
    }
}
