<?php

declare(strict_types=1);

namespace Manguithre\FakerMadagascar\Generators;

use Manguithre\FakerMadagascar\Data\NameRepository;

class PersonGenerator
{
    public function firstName(): string
    {
        return $this->pick(NameRepository::firstNames());
    }

    public function lastName(): string
    {
        return $this->pick(NameRepository::lastNames());
    }

    public function fullName(): string
    {
        return $this->firstName() . ' ' . $this->lastName();
    }

    private function pick(array $items): string
    {
        return $items[array_rand($items)];
    }
}
