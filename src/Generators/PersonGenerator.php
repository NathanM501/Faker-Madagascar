<?php

declare(strict_types=1);

namespace Manguithre\FakerMadagascar\Generators;

use Manguithre\FakerMadagascar\Data\NameSyllableRepository;

class PersonGenerator
{
    public function __construct()
    {
    }

    public function firstName(): string
    {
        $prefix = $this->pick(NameSyllableRepository::prefixes('first_name'));
        $middle = $this->pick(NameSyllableRepository::middles('first_name'));

        $parts = [$prefix, $middle];

        if ($this->shouldAddSuffix()) {
            $suffix = $this->pick(NameSyllableRepository::suffixes('first_name'));

            if ($suffix !== $middle) {
                $parts[] = $suffix;
            }
        }

        return implode('', $parts);
    }

    public function lastName(): string
    {
        $prefix = $this->pick(NameSyllableRepository::prefixes('last_name'));
        $middle = $this->pick(NameSyllableRepository::middles('last_name'));
        $parts = [$prefix, $middle];

        if ($this->shouldAddSuffix()) {
            $suffix = $this->pick(NameSyllableRepository::suffixes('last_name'));

            if ($suffix !== $middle) {
                $parts[] = $suffix;
            }
        }

        return implode('', $parts);
    }

    public function fullName(): string
    {
        return $this->firstName() . ' ' . $this->lastName();
    }

    private function shouldAddSuffix(): bool
    {
        return random_int(1, 100) <= 50;
    }

    private function pick(array $items): string
    {
        return $items[array_rand($items)];
    }
}
