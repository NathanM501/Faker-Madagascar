<?php

declare(strict_types=1);

namespace Manguithre\FakerMadagascar\Faker;

use Manguithre\FakerMadagascar\FakerMadagascar;
use Manguithre\FakerMadagascar\ValueObjects\MalagasyAddress;

/**
 * FakerPHP provider exposing Malagasy data through any Faker Generator:
 *
 *     $faker->addProvider(new MalagasyProvider($fakerMadagascar));
 *     $faker->malagasyAddress();
 *     $faker->malagasyFullName();
 */
class MalagasyProvider
{
    public function __construct(protected FakerMadagascar $fakerMadagascar) {}

    public function malagasyAddress(?string $region = null): MalagasyAddress
    {
        return $this->fakerMadagascar->address($region);
    }

    public function malagasyRegion(): string
    {
        return $this->fakerMadagascar->region();
    }

    public function malagasyDistrict(string $region): string
    {
        return $this->fakerMadagascar->district($region);
    }

    public function malagasyCommune(string $region, string $district): string
    {
        return $this->fakerMadagascar->commune($region, $district);
    }

    public function malagasyFokontany(string $region, string $district, string $commune): string
    {
        return $this->fakerMadagascar->fokontany($region, $district, $commune);
    }

    public function malagasyFirstName(): string
    {
        return $this->fakerMadagascar->firstName();
    }

    public function malagasyLastName(): string
    {
        return $this->fakerMadagascar->lastName();
    }

    public function malagasyFullName(): string
    {
        return $this->fakerMadagascar->fullName();
    }

    public function malagasyPhoneNumber(?string $operator = null): string
    {
        return $this->fakerMadagascar->phoneNumber($operator);
    }

    public function malagasyCin(): string
    {
        return $this->fakerMadagascar->cin();
    }
}
