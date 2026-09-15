<?php

declare(strict_types=1);

namespace Manguithre\FakerMadagascar\Tests\Unit;

use Manguithre\FakerMadagascar\Data\NameRepository;
use Manguithre\FakerMadagascar\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class NameRepositoryTest extends TestCase
{
    #[Test]
    public function it_returns_first_names(): void
    {
        $names = NameRepository::firstNames();

        $this->assertNotEmpty($names);
        $this->assertContains('Rakoto', $names);
    }

    #[Test]
    public function it_returns_last_names(): void
    {
        $names = NameRepository::lastNames();

        $this->assertNotEmpty($names);
        $this->assertContains('Rakotomalala', $names);
    }

    #[Test]
    public function it_is_consistent_across_calls(): void
    {
        $first = NameRepository::firstNames();
        $second = NameRepository::firstNames();

        $this->assertSame($first, $second);
    }

    #[Test]
    public function it_returns_only_strings(): void
    {
        foreach (NameRepository::firstNames() as $name) {
            $this->assertIsString($name);
        }

        foreach (NameRepository::lastNames() as $name) {
            $this->assertIsString($name);
        }
    }
}
