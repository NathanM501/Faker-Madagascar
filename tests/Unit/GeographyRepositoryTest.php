<?php

declare(strict_types=1);

namespace Manguithre\FakerMadagascar\Tests\Unit;

use Manguithre\FakerMadagascar\Data\GeographyRepository;
use Manguithre\FakerMadagascar\Exceptions\InvalidArgumentException;
use Manguithre\FakerMadagascar\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class GeographyRepositoryTest extends TestCase
{
    private GeographyRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new GeographyRepository;
    }

    #[Test]
    public function it_returns_all_regions(): void
    {
        $regions = $this->repository->regions();

        $this->assertNotEmpty($regions);
        $this->assertContains('ANALAMANGA', $regions);
        $this->assertContains('ATSINANANA', $regions);
    }

    #[Test]
    public function it_returns_random_region(): void
    {
        $region = $this->repository->randomRegion();

        $this->assertContains($region, $this->repository->regions());
    }

    #[Test]
    public function it_filters_regions_by_active_regions_config(): void
    {
        config(['faker-madagascar.active_regions' => ['ANALAMANGA']]);

        $regions = $this->repository->regions();

        $this->assertSame(['ANALAMANGA'], $regions);
        $this->assertSame('ANALAMANGA', $this->repository->randomRegion());
    }

    #[Test]
    public function it_filters_active_regions_case_insensitively(): void
    {
        config(['faker-madagascar.active_regions' => ['Analamanga']]);

        $this->assertSame(['ANALAMANGA'], $this->repository->regions());
    }

    #[Test]
    public function it_returns_districts_in_region(): void
    {
        $districts = $this->repository->districtsIn('ANALAMANGA');

        $this->assertContains('Antananarivo Atsimondrano', $districts);
        $this->assertContains('Antananarivo Avaradrano', $districts);
    }

    #[Test]
    public function it_resolves_region_names_case_insensitively(): void
    {
        $this->assertSame('ANALAMANGA', $this->repository->resolveRegion('Analamanga'));
        $this->assertSame('ANALAMANGA', $this->repository->resolveRegion('analamanga'));
        $this->assertSame('ANALAMANGA', $this->repository->resolveRegion('ANALAMANGA'));
    }

    #[Test]
    public function it_throws_for_unknown_region_on_resolve(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->repository->resolveRegion('UNKNOWN-REGION');
    }

    #[Test]
    public function it_throws_when_forced_region_is_not_active(): void
    {
        config(['faker-madagascar.active_regions' => ['ATSINANANA']]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('active_regions');

        $this->repository->resolveActiveRegion('ANALAMANGA');
    }

    #[Test]
    public function it_returns_random_district_in_region(): void
    {
        $district = $this->repository->randomDistrictIn('ANALAMANGA');

        $this->assertContains($district, $this->repository->districtsIn('ANALAMANGA'));
    }

    #[Test]
    public function it_returns_communes_in_district(): void
    {
        $communes = $this->repository->communesIn('ANALAMANGA', 'Antananarivo Atsimondrano');

        $this->assertContains('Alakamisy Fenoarivo', $communes);
    }

    #[Test]
    public function it_returns_random_commune_in_district(): void
    {
        $commune = $this->repository->randomCommuneIn('ANALAMANGA', 'Antananarivo Atsimondrano');

        $this->assertContains($commune, $this->repository->communesIn('ANALAMANGA', 'Antananarivo Atsimondrano'));
    }

    #[Test]
    public function it_returns_fokontany_in_commune(): void
    {
        $fokontany = $this->repository->fokontanyIn('ANALAMANGA', 'Antananarivo Atsimondrano', 'Alakamisy Fenoarivo');

        $this->assertContains('Ankadivory', $fokontany);
    }

    #[Test]
    public function it_ships_no_placeholder_fokontany(): void
    {
        // Regression guard: every commune must have real fokontany names
        // (from julkwel/madagascar-map), not synthetic FOKONTANY-* entries.
        foreach ($this->repository->all()['regions'] as $region) {
            foreach ($region['districts'] ?? [] as $district) {
                foreach ($district['communes'] ?? [] as $commune) {
                    foreach ($commune['fokontany'] ?? [] as $fokontany) {
                        $this->assertStringStartsNotWith('FOKONTANY-', $fokontany);

                        return;
                    }
                }
            }
        }
    }

    #[Test]
    public function it_throws_exception_for_unknown_region(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->repository->districtsIn('UNKNOWN-REGION');
    }

    #[Test]
    public function it_throws_exception_for_unknown_district(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->repository->communesIn('ANALAMANGA', 'UNKNOWN-DISTRICT');
    }
}
