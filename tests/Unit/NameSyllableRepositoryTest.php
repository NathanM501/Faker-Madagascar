<?php

declare(strict_types=1);

namespace Manguithre\FakerMadagascar\Tests\Unit;

use Manguithre\FakerMadagascar\Data\NameSyllableRepository;
use Manguithre\FakerMadagascar\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class NameSyllableRepositoryTest extends TestCase
{
    #[Test]
    public function it_returns_prefixes_for_first_name(): void
    {
        $prefixes = NameSyllableRepository::prefixes('first_name');

        $this->assertNotEmpty($prefixes);
        $this->assertContains('Andri', $prefixes);
    }

    #[Test]
    public function it_returns_middles_for_first_name(): void
    {
        $this->assertNotEmpty(NameSyllableRepository::middles('first_name'));
    }

    #[Test]
    public function it_returns_suffixes_for_first_name(): void
    {
        $this->assertNotEmpty(NameSyllableRepository::suffixes('first_name'));
    }

    #[Test]
    public function it_returns_prefixes_for_last_name(): void
    {
        $prefixes = NameSyllableRepository::prefixes('last_name');

        $this->assertNotEmpty($prefixes);
        $this->assertContains('Andrian', $prefixes);
    }

    #[Test]
    public function it_throws_exception_for_unknown_type(): void
    {
        $this->expectException(\UnexpectedValueException::class);

        NameSyllableRepository::prefixes('unknown_type');
    }
}
