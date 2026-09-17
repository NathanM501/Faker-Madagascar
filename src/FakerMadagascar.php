<?php

declare(strict_types=1);

namespace Manguithre\FakerMadagascar;

use Manguithre\FakerMadagascar\Data\PhonePrefixRepository;
use Manguithre\FakerMadagascar\Exceptions\InvalidArgumentException;
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

    /**
     * Generate a random hierarchical address.
     *
     * @param string|null $region Restrict the address to a specific region
     *                             (case-insensitive). When null, a random
     *                             region is selected based on the
     *                             active_regions config.
     *
     * @return MalagasyAddress
     *
     * @throws InvalidArgumentException When the region does not exist or
     *                                   is not in the active_regions config.
     */
    public function address(?string $region = null): MalagasyAddress
    {
        return $this->addressGenerator->addressInRegion($region ?? $this->addressGenerator->randomRegion());
    }

    /**
     * Generate an address inside a specific district.
     *
     * @param string $region   Canonical region name (case-insensitive).
     * @param string $district Exact district name as in the dataset.
     *
     * @return MalagasyAddress
     *
     * @throws InvalidArgumentException When the region or district is not found.
     */
    public function addressInDistrict(string $region, string $district): MalagasyAddress
    {
        return $this->addressGenerator->addressInDistrict($region, $district);
    }

    /**
     * Generate an address inside a specific commune.
     *
     * @param string $region   Canonical region name (case-insensitive).
     * @param string $district Exact district name as in the dataset.
     * @param string $commune  Exact commune name as in the dataset.
     *
     * @return MalagasyAddress
     *
     * @throws InvalidArgumentException When the region, district, or commune is not found.
     */
    public function addressInCommune(string $region, string $district, string $commune): MalagasyAddress
    {
        return $this->addressGenerator->addressInCommune($region, $district, $commune);
    }

    /**
     * Return a random region name.
     *
     * Respects the active_regions config: only returns regions that
     * are in the configured list, or all regions if the list is empty.
     */
    public function region(): string
    {
        return $this->addressGenerator->randomRegion();
    }

    /**
     * Return all region names.
     *
     * Respects the active_regions config: only returns regions that
     * are in the configured list, or all regions if the list is empty.
     *
     * @return list<string>
     */
    public function regions(): array
    {
        return $this->addressGenerator->regions();
    }

    /**
     * List the districts of a region.
     *
     * @param string $region Canonical region name (case-insensitive).
     *
     * @return list<string>
     *
     * @throws InvalidArgumentException When the region is not found.
     */
    public function districts(string $region): array
    {
        return $this->addressGenerator->districts($region);
    }

    /**
     * Return a random district in a region.
     *
     * @param string $region Canonical region name (case-insensitive).
     *
     * @return string
     *
     * @throws InvalidArgumentException When the region is not found or has no districts.
     */
    public function district(string $region): string
    {
        return $this->addressGenerator->randomDistrictIn($region);
    }

    /**
     * List the communes of a district.
     *
     * @param string $region   Canonical region name (case-insensitive).
     * @param string $district Exact district name as in the dataset.
     *
     * @return list<string>
     *
     * @throws InvalidArgumentException When the region or district is not found.
     */
    public function communes(string $region, string $district): array
    {
        return $this->addressGenerator->communes($region, $district);
    }

    /**
     * Return a random commune in a district.
     *
     * @param string $region   Canonical region name (case-insensitive).
     * @param string $district Exact district name as in the dataset.
     *
     * @return string
     *
     * @throws InvalidArgumentException When the region or district is not found.
     */
    public function commune(string $region, string $district): string
    {
        return $this->addressGenerator->randomCommuneIn($region, $district);
    }

    /**
     * Return a random fokontany in a commune.
     *
     * @param string $region   Canonical region name (case-insensitive).
     * @param string $district Exact district name as in the dataset.
     * @param string $commune  Exact commune name as in the dataset.
     *
     * @return string
     *
     * @throws InvalidArgumentException When the region, district, or commune is not found.
     */
    public function fokontany(string $region, string $district, string $commune): string
    {
        return $this->addressGenerator->randomFokontanyIn($region, $district, $commune);
    }

    /**
     * Generate a Malagasy first name.
     */
    public function firstName(): string
    {
        return $this->personGenerator->firstName();
    }

    /**
     * Generate a Malagasy last name.
     */
    public function lastName(): string
    {
        return $this->personGenerator->lastName();
    }

    /**
     * Generate a Malagasy full name (first + last).
     */
    public function fullName(): string
    {
        return $this->personGenerator->fullName();
    }

    /**
     * Generate a Malagasy mobile number.
     *
     * @param string|null $operator Optional operator name (e.g. 'orange', 'airtel',
     *                               'telma', 'bip'). When null, a random operator
     *                               is selected from the verified prefix list.
     *
     * @return string 10-digit mobile number starting with a valid operator prefix.
     *
     * @throws InvalidArgumentException When the operator is unknown.
     */
    public function phoneNumber(?string $operator = null): string
    {
        return $this->contactGenerator->phoneNumber($operator);
    }

    /**
     * Return all valid Malagasy mobile prefixes.
     *
     * @return list<string> Sorted list of 10-digit prefixes (e.g. '032', '033', ...).
     */
    public function phonePrefixes(): array
    {
        return PhonePrefixRepository::all();
    }

    /**
     * Generate a 12-digit CIN-formatted value.
     *
     * The Malagasy CIN (Carte d'Identité Nationale) is exactly 12 digits
     * with no separators. This generates a random value in that format.
     * It does not verify whether the CIN was officially issued.
     */
    public function cin(): string
    {
        return $this->contactGenerator->cin();
    }
}
