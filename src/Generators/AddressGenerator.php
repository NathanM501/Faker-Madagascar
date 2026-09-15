<?php

declare(strict_types=1);

namespace Manguithre\FakerMadagascar\Generators;

use Manguithre\FakerMadagascar\Data\GeographyRepository;
use Manguithre\FakerMadagascar\ValueObjects\MalagasyAddress;

class AddressGenerator
{
    public function __construct(private GeographyRepository $geography = new GeographyRepository())
    {
    }

    public function address(): MalagasyAddress
    {
        return $this->addressInRegion($this->geography->randomRegion());
    }

    public function addressInRegion(string $region): MalagasyAddress
    {
        $region = $this->geography->resolveActiveRegion($region);
        $district = $this->geography->randomDistrictIn($region);

        return $this->addressInDistrict($region, $district);
    }

    public function addressInDistrict(string $region, string $district): MalagasyAddress
    {
        $commune = $this->geography->randomCommuneIn($region, $district);

        return $this->addressInCommune($this->geography->resolveRegion($region), $district, $commune);
    }

    public function addressInCommune(string $region, string $district, string $commune): MalagasyAddress
    {
        $fokontany = $this->geography->randomFokontanyIn($region, $district, $commune);

        return new MalagasyAddress(
            $this->geography->resolveRegion($region),
            $district,
            $commune,
            $fokontany,
        );
    }

    public function randomRegion(): string
    {
        return $this->geography->randomRegion();
    }

    public function randomDistrictIn(string $region): string
    {
        return $this->geography->randomDistrictIn($region);
    }

    public function randomCommuneIn(string $region, string $district): string
    {
        return $this->geography->randomCommuneIn($region, $district);
    }

    public function randomFokontanyIn(string $region, string $district, string $commune): string
    {
        return $this->geography->randomFokontanyIn($region, $district, $commune);
    }

    public function districts(string $region): array
    {
        return $this->geography->districtsIn($region);
    }

    public function communes(string $region, string $district): array
    {
        return $this->geography->communesIn($region, $district);
    }

    public function fokontany(string $region, string $district, string $commune): array
    {
        return $this->geography->fokontanyIn($region, $district, $commune);
    }
}
