<?php

declare(strict_types=1);

namespace Manguithre\FakerMadagascar\Generators;

use Manguithre\FakerMadagascar\Data\PhonePrefixRepository;

class ContactGenerator
{
    public function __construct()
    {
    }

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

        for ($i = 0; $i < 7; ++$i) {
            $body .= random_int(0, 9);
        }

        return $prefix . $body;
    }

    public function cin(): string
    {
        // The real Malagasy CIN is a 12-digit number with no separators (XXXXXXXXXXXX).
        $cin = '';

        for ($i = 0; $i < 12; ++$i) {
            $cin .= random_int(0, 9);
        }

        return $cin;
    }
}
