<?php

declare(strict_types=1);

namespace Manguithre\FakerMadagascar\Data;

use Manguithre\FakerMadagascar\Exceptions\InvalidArgumentException;
use Manguithre\FakerMadagascar\Support\Config;

class GeographyRepository
{
    private static ?array $cache = null;

    /**
     * Raw geography data (all regions, ignoring active_regions).
     */
    public function all(): array
    {
        if (self::$cache === null) {
            $file = __DIR__ . '/../../resources/data/geography.json';

            if (!file_exists($file)) {
                throw new \UnexpectedValueException("Geography data file not found: $file");
            }

            self::$cache = json_decode((string) file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
        }

        return self::$cache;
    }

    /**
     * Regions available for generation, filtered by the active_regions config.
     *
     * @return list<string>
     */
    public function regions(): array
    {
        $regions = array_keys($this->all()['regions'] ?? []);
        $active = Config::get('active_regions', []);

        if (is_array($active) && $active !== []) {
            $regions = array_values(array_filter(
                $regions,
                fn (string $region): bool => in_array($region, $active, true) || $this->caseInsensitiveMatch($region, $active) !== null,
            ));
        }

        return $regions;
    }

    /**
     * Resolve a user-supplied region name to its canonical key,
     * case-insensitively (e.g. 'Analamanga' -> 'ANALAMANGA').
     */
    public function resolveRegion(string $region): string
    {
        $all = array_keys($this->all()['regions'] ?? []);

        if (in_array($region, $all, true)) {
            return $region;
        }

        $match = $this->caseInsensitiveMatch($region, $all);

        if ($match !== null) {
            return $match;
        }

        throw new InvalidArgumentException("Region not found: $region");
    }

    /**
     * @return list<string>
     */
    public function districtsIn(string $region): array
    {
        $region = $this->resolveRegion($region);

        return array_keys($this->all()['regions'][$region]['districts'] ?? []);
    }

    public function randomRegion(): string
    {
        $regions = $this->regions();

        if ($regions === []) {
            throw new \UnexpectedValueException('No active regions available in geography data.');
        }

        return $regions[array_rand($regions)];
    }

    public function randomDistrictIn(string $region): string
    {
        $districts = $this->districtsIn($region);

        if ($districts === []) {
            throw new \UnexpectedValueException("No districts found for region: $region");
        }

        return $districts[array_rand($districts)];
    }

    /**
     * @return list<string>
     */
    public function communesIn(string $region, string $district): array
    {
        $region = $this->resolveRegion($region);
        $node = $this->all()['regions'][$region]['districts'][$district] ?? null;

        if ($node === null) {
            throw new InvalidArgumentException("District not found: $district in region $region");
        }

        return array_keys($node['communes'] ?? []);
    }

    public function randomCommuneIn(string $region, string $district): string
    {
        $communes = $this->communesIn($region, $district);

        if ($communes === []) {
            throw new \UnexpectedValueException("No communes found for district: $district in region $region");
        }

        return $communes[array_rand($communes)];
    }

    /**
     * @return list<string>
     */
    public function fokontanyIn(string $region, string $district, string $commune): array
    {
        $region = $this->resolveRegion($region);
        $node = $this->all()['regions'][$region]['districts'][$district]['communes'][$commune] ?? null;

        if ($node === null) {
            throw new InvalidArgumentException("Commune not found: $commune in district $district, region $region");
        }

        return $node['fokontany'] ?? [];
    }

    public function randomFokontanyIn(string $region, string $district, string $commune): string
    {
        $fokontany = $this->fokontanyIn($region, $district, $commune);

        if ($fokontany === []) {
            throw new \UnexpectedValueException("No fokontany found for commune: $commune");
        }

        return $fokontany[array_rand($fokontany)];
    }

    /**
     * Resolve a region and enforce the active_regions config.
     *
     * Throws when the region does not exist, or exists but is filtered out
     * by the active_regions config (unless no filter is configured).
     */
    public function resolveActiveRegion(string $region): string
    {
        $resolved = $this->resolveRegion($region);
        $active = Config::get('active_regions', []);

        if (is_array($active) && $active !== [] && !in_array($resolved, $this->regions(), true)) {
            throw new InvalidArgumentException("Region is not in active_regions config: $resolved");
        }

        return $resolved;
    }

    /**
     * Find a case-insensitive match for $needle among $candidates.
     */
    private function caseInsensitiveMatch(string $needle, array $candidates): ?string
    {
        foreach ($candidates as $candidate) {
            if (strcasecmp($needle, (string) $candidate) === 0) {
                return (string) $candidate;
            }
        }

        return null;
    }
}
