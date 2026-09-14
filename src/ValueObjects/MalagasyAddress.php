<?php

declare(strict_types=1);

namespace Manguithre\FakerMadagascar\ValueObjects;

readonly class MalagasyAddress
{
    public function __construct(
        public string $region,
        public string $district,
        public string $commune,
        public string $fokontany,
    ) {
    }

    public function __toString(): string
    {
        return sprintf('%s, %s, %s, %s', $this->commune, $this->district, $this->region, $this->fokontany);
    }

    public function toArray(): array
    {
        return [
            'region' => $this->region,
            'district' => $this->district,
            'commune' => $this->commune,
            'fokontany' => $this->fokontany,
        ];
    }
}
