<?php

declare(strict_types=1);

namespace Manguithre\FakerMadagascar\Tests\Feature;

use Faker\Generator;
use Manguithre\FakerMadagascar\Faker\MalagasyProvider;
use Manguithre\FakerMadagascar\FakerMadagascar;
use Manguithre\FakerMadagascar\FakerMadagascarServiceProvider;
use Manguithre\FakerMadagascar\Generators\AddressGenerator;
use Manguithre\FakerMadagascar\Generators\ContactGenerator;
use Manguithre\FakerMadagascar\Generators\PersonGenerator;
use Manguithre\FakerMadagascar\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class ServiceProviderTest extends TestCase
{
    #[Test]
    public function it_registers_provider(): void
    {
        $this->assertInstanceOf(
            FakerMadagascarServiceProvider::class,
            $this->app->getProvider(FakerMadagascarServiceProvider::class),
        );
    }

    #[Test]
    public function it_registers_faker_madagascar_as_singleton(): void
    {
        $this->assertSame($this->app->make(FakerMadagascar::class), $this->app->make(FakerMadagascar::class));
    }

    #[Test]
    public function it_binds_generators_as_singletons(): void
    {
        $this->assertInstanceOf(AddressGenerator::class, $this->app->make(AddressGenerator::class));
        $this->assertInstanceOf(PersonGenerator::class, $this->app->make(PersonGenerator::class));
        $this->assertInstanceOf(ContactGenerator::class, $this->app->make(ContactGenerator::class));
    }

    #[Test]
    public function it_resolves_faker_generator_with_malagasy_provider_attached(): void
    {
        $faker = $this->app->make(Generator::class);

        $malagasyMethods = array_map(
            fn (object $provider): array => array_filter(
                get_class_methods($provider),
                fn (string $method): bool => str_starts_with($method, 'malagasy'),
            ),
            $faker->getProviders(),
        );

        $methods = array_merge(...array_values($malagasyMethods));

        $this->assertContains('malagasyAddress', $methods);
        $this->assertContains('malagasyFullName', $methods);
        $this->assertContains('malagasyPhoneNumber', $methods);
    }

    #[Test]
    public function it_generates_through_faker_magic_methods(): void
    {
        $faker = $this->app->make(Generator::class);

        $address = $faker->malagasyAddress();

        $this->assertNotEmpty($address->region);
        $this->assertMatchesRegularExpression('/^0(32|33|34|35|37|38|39)\d{7}$/', $faker->malagasyPhoneNumber());
    }
}
