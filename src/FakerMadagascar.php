<?php

declare(strict_types=1);

namespace Manguithre\FakerMadagascar;

use Manguithre\FakerMadagascar\Data\PhonePrefixRepository;
use Manguithre\FakerMadagascar\Generators\AddressGenerator;
use Manguithre\FakerMadagascar\Generators\ContactGenerator;
use Manguithre\FakerMadagascar\Generators\PersonGenerator;
use Manguithre\FakerMadagascar\ValueObjects\MalagasyAddress;

class FakerMadagascar
{
    public function __construct(
        private AddressGenerator $addressGenerator,
        private PersonGenerator $personGenerator,
        private ContactGenerator $contactGenerator,
    ) {
    }

    public function address(?string $region = null): MalagasyAddress
    {
        return $this->addressGenerator->addressInRegion($region ?? $this->addressGenerator->randomRegion());
    }

    public function addressInDistrict(string $region, string $district): MalagasyAddress
    {
        return $this->addressGenerator->addressInDistrict($region, $district);
    }

    public function addressInCommune(string $region, string $district, string $commune): MalagasyAddress
    {
        return $this->addressGenerator->addressInCommune($region, $district, $commune);
    }

    public function region(): string
    {
        return $this->addressGenerator->randomRegion();
    }

    public function districts(string $region): array
    {
        return $this->addressGenerator->districtsIn($region);
    }

    public function district(string $region): string
    {
        return $this->addressGenerator->randomDistrictIn($region);
    }

    public function communes(string $region, string $district): array
    {
        return $this->addressGenerator->communesIn($region, $district);
    }

    public function commune(string $region, string $district): string
    {
        return $this->addressGenerator->randomCommuneIn($region, $district);
    }

    public function fokontany(string $region, string $district, string $commune): string
    {
        return $this->addressGenerator->randomFokontanyIn($region, $district, $commune);
    }

    public function firstName(): string
    {
        return $this->personGenerator->firstName();
    }

    public function lastName(): string
    {
        return $this->personGenerator->lastName();
    }

    public function fullName(): string
    {
        return $this->personGenerator->fullName();
    }

    public function phoneNumber(?string $operator = null): string
    {
        return $this->contactGenerator->phoneNumber($operator);
    }

    /**
     * All valid Malagasy mobile prefixes (single source of truth).
     *
     * @return list<string>
     */
    public function phonePrefixes(): array
    {
        return PhonePrefixRepository::all();
    }

    public function cin(): string
    {
        return $this->contactGenerator->cin();
    }
}
