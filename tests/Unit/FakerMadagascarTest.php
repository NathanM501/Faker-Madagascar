<?php

declare(strict_types=1);

namespace Manguithre\FakerMadagascar\Tests\Unit;

use Manguithre\FakerMadagascar\Data\PhonePrefixRepository;
use Manguithre\FakerMadagascar\Exceptions\InvalidArgumentException;
use Manguithre\FakerMadagascar\FakerMadagascar;
use Manguithre\FakerMadagascar\Tests\TestCase;
use Manguithre\FakerMadagascar\ValueObjects\MalagasyAddress;
use PHPUnit\Framework\Attributes\Test;

final class FakerMadagascarTest extends TestCase
{
    private FakerMadagascar $faker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = $this->app->make(FakerMadagascar::class);
    }

    #[Test]
    public function it_generates_a_random_address(): void
    {
        $address = $this->faker->address();

        $this->assertInstanceOf(MalagasyAddress::class, $address);
        $this->assertContains($address->region, $this->app->make(\Manguithre\FakerMadagascar\Data\GeographyRepository::class)->regions());
    }

    #[Test]
    public function it_generates_an_address_for_a_forced_region(): void
    {
        $this->assertSame('ATSINANANA', $this->faker->address(region: 'Atsinanana')->region);
    }

    #[Test]
    public function it_throws_for_inactive_forced_region(): void
    {
        config(['faker-madagascar.active_regions' => ['ANALAMANGA']]);

        $this->expectException(InvalidArgumentException::class);

        $this->faker->address(region: 'Atsinanana');
    }

    #[Test]
    public function it_generates_address_in_district(): void
    {
        $address = $this->faker->addressInDistrict('ANALAMANGA', 'Antananarivo Atsimondrano');

        $this->assertSame('ANALAMANGA', $address->region);
        $this->assertSame('Antananarivo Atsimondrano', $address->district);
    }

    #[Test]
    public function it_generates_names(): void
    {
        $this->assertNotEmpty($this->faker->firstName());
        $this->assertNotEmpty($this->faker->lastName());
        $this->assertStringContainsString(' ', $this->faker->fullName());
    }

    #[Test]
    public function it_generates_contact_data(): void
    {
        $phone = $this->faker->phoneNumber();

        $this->assertTrue(PhonePrefixRepository::isValidPrefix($phone));
        $this->assertSame(10, strlen($phone));
        $this->assertMatchesRegularExpression('/^[0-9]{12}$/', $this->faker->cin());
    }

    #[Test]
    public function it_exposes_the_verified_prefixes(): void
    {
        $this->assertSame(PhonePrefixRepository::all(), $this->faker->phonePrefixes());
    }

    #[Test]
    public function it_exposes_geography_helpers(): void
    {
        $this->assertContains('Antananarivo Atsimondrano', $this->faker->districts('ANALAMANGA'));
        $this->assertNotEmpty($this->faker->district('ANALAMANGA'));
        $this->assertNotEmpty($this->faker->commune('ANALAMANGA', 'Antananarivo Atsimondrano'));
        $this->assertNotEmpty($this->faker->fokontany('ANALAMANGA', 'Antananarivo Atsimondrano', 'Alakamisy Fenoarivo'));
    }
}
