<?php

declare(strict_types=1);

namespace Manguithre\FakerMadagascar\Tests\Unit;

use Manguithre\FakerMadagascar\Data\PhonePrefixRepository;
use Manguithre\FakerMadagascar\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class PhonePrefixRepositoryTest extends TestCase
{
    #[Test]
    public function it_returns_the_verified_operator_prefixes(): void
    {
        $this->assertSame(
            ['032', '033', '034', '035', '037', '038', '039'],
            PhonePrefixRepository::all(),
        );
    }

    #[Test]
    public function it_returns_prefixes_for_each_operator(): void
    {
        $this->assertSame(['032', '037'], PhonePrefixRepository::forOperator('orange'));
        $this->assertSame(['033', '035'], PhonePrefixRepository::forOperator('airtel'));
        $this->assertSame(['034', '038'], PhonePrefixRepository::forOperator('telma'));
        $this->assertSame(['039'], PhonePrefixRepository::forOperator('bip'));
    }

    #[Test]
    public function it_is_case_insensitive_for_operators(): void
    {
        $this->assertSame(['032', '037'], PhonePrefixRepository::forOperator('ORANGE'));
    }

    #[Test]
    public function it_returns_empty_for_unknown_operator(): void
    {
        $this->assertSame([], PhonePrefixRepository::forOperator('unknown'));
    }

    #[Test]
    public function it_lists_all_operators(): void
    {
        $this->assertSame(['orange', 'airtel', 'telma', 'bip'], PhonePrefixRepository::operators());
    }

    #[Test]
    public function it_validates_national_number_prefixes(): void
    {
        $this->assertTrue(PhonePrefixRepository::isValidPrefix('0321234567'));
        $this->assertTrue(PhonePrefixRepository::isValidPrefix('0351234567'));
        $this->assertTrue(PhonePrefixRepository::isValidPrefix('0391234567'));
        $this->assertFalse(PhonePrefixRepository::isValidPrefix('0361234567'));
        $this->assertFalse(PhonePrefixRepository::isValidPrefix('0311234567'));
    }
}
