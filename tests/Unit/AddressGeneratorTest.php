<?php

declare(strict_types=1);

namespace Manguithre\FakerMadagascar\Tests\Unit;

use Manguithre\FakerMadagascar\Data\GeographyRepository;
use Manguithre\FakerMadagascar\Exceptions\InvalidArgumentException;
use Manguithre\FakerMadagascar\Generators\AddressGenerator;
use Manguithre\FakerMadagascar\ValueObjects\MalagasyAddress;
use Manguithre\FakerMadagascar\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class AddressGeneratorTest extends TestCase
{
    private AddressGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->generator = new AddressGenerator(new GeographyRepository);
    }

    #[Test]
    public function it_returns_a_malagasy_address(): void
    {
        $address = $this->generator->address();

        $this->assertInstanceOf(MalagasyAddress::class, $address);
        $this->assertNotEmpty($address->region);
        $this->assertNotEmpty($address->district);
        $this->assertNotEmpty($address->commune);
        $this->assertNotEmpty($address->fokontany);
    }

    #[Test]
    public function it_generates_address_in_specific_region(): void
    {
        $address = $this->generator->addressInRegion('ANALAMANGA');

        $this->assertSame('ANALAMANGA', $address->region);
    }

    #[Test]
    public function it_resolves_region_names_case_insensitively(): void
    {
        $address = $this->generator->addressInRegion('Analamanga');

        $this->assertSame('ANALAMANGA', $address->region);
    }

    #[Test]
    public function it_generates_address_in_specific_district(): void
    {
        $address = $this->generator->addressInDistrict('ANALAMANGA', 'Antananarivo Atsimondrano');

        $this->assertSame('ANALAMANGA', $address->region);
        $this->assertSame('Antananarivo Atsimondrano', $address->district);
    }

    #[Test]
    public function it_generates_address_in_specific_commune(): void
    {
        $address = $this->generator->addressInCommune('ANALAMANGA', 'Antananarivo Atsimondrano', 'Alakamisy Fenoarivo');

        $this->assertSame('ANALAMANGA', $address->region);
        $this->assertSame('Antananarivo Atsimondrano', $address->district);
        $this->assertSame('Alakamisy Fenoarivo', $address->commune);
    }

    #[Test]
    public function it_returns_string_representation(): void
    {
        $address = $this->generator->addressInCommune('ANALAMANGA', 'Antananarivo Atsimondrano', 'Alakamisy Fenoarivo');

        $fokontany = $address->fokontany;

        $this->assertSame("Alakamisy Fenoarivo, Antananarivo Atsimondrano, ANALAMANGA, $fokontany", (string) $address);
    }

    #[Test]
    public function it_converts_to_array(): void
    {
        $address = $this->generator->addressInCommune('ANALAMANGA', 'Antananarivo Atsimondrano', 'Alakamisy Fenoarivo');

        $this->assertSame([
            'region' => 'ANALAMANGA',
            'district' => 'Antananarivo Atsimondrano',
            'commune' => 'Alakamisy Fenoarivo',
            'fokontany' => $address->fokontany,
        ], $address->toArray());
    }

    #[Test]
    public function it_uses_real_fokontany_names(): void
    {
        // Alakamisy Fenoarivo has real fokontany in the data set
        // (e.g. Ankadivory, Ambodivona) — never the old FOKONTANY-CENTRE placeholder.
        $address = $this->generator->addressInCommune('ANALAMANGA', 'Antananarivo Atsimondrano', 'Alakamisy Fenoarivo');

        $this->assertNotSame('FOKONTANY-CENTRE', $address->fokontany);
        $this->assertContains($address->fokontany, ['Ankadivory', 'Ambodivona', 'Ambohimasina', 'Ambohimiarina', 'Antanety II']);
    }

    #[Test]
    public function it_throws_exception_for_invalid_region(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->generator->addressInRegion('UNKNOWN-REGION');
    }

    #[Test]
    public function it_throws_exception_for_invalid_district(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->generator->addressInDistrict('ANALAMANGA', 'UNKNOWN-DISTRICT');
    }

    #[Test]
    public function it_throws_exception_for_region_not_in_active_regions(): void
    {
        config(['faker-madagascar.active_regions' => ['ATSINANANA']]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('active_regions');

        $this->generator->addressInRegion('ANALAMANGA');
    }

    #[Test]
    public function it_allows_active_regions_and_randomizes_within_them(): void
    {
        config(['faker-madagascar.active_regions' => ['ANALAMANGA']]);

        for ($i = 0; $i < 10; $i++) {
            $this->assertSame('ANALAMANGA', $this->generator->address()->region);
        }
    }

    #[Test]
    public function it_accepts_active_regions_case_insensitively(): void
    {
        config(['faker-madagascar.active_regions' => ['Analamanga']]);

        $this->assertSame('ANALAMANGA', $this->generator->addressInRegion('ANALAMANGA')->region);
        $this->assertSame('ANALAMANGA', $this->generator->address()->region);
    }
}
