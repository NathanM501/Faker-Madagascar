<?php

declare(strict_types=1);

namespace Manguithre\FakerMadagascar\Tests\Unit;

use Manguithre\FakerMadagascar\FakerMadagascar;
use Manguithre\FakerMadagascar\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class HelperTest extends TestCase
{
    #[Test]
    public function it_resolves_the_container_singleton_inside_laravel(): void
    {
        $first = fakerMg();
        $second = fakerMg();

        $this->assertInstanceOf(FakerMadagascar::class, $first);
        $this->assertSame($first, $second);
    }

    #[Test]
    public function it_generates_a_valid_phone_number_through_the_helper(): void
    {
        $this->assertMatchesRegularExpression('/^0(32|33|34|35|37|38|39)\d{7}$/', fakerMg()->phoneNumber());
    }

    #[Test]
    public function it_generates_a_valid_address_through_the_helper(): void
    {
        $address = fakerMg()->address();

        $this->assertNotSame('', $address->region);
        $this->assertNotSame('', $address->district);
        $this->assertNotSame('', $address->commune);
    }
}
