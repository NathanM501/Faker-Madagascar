<?php

declare(strict_types=1);

namespace Manguithre\FakerMadagascar\Tests\Unit;

use Manguithre\FakerMadagascar\Generators\PersonGenerator;
use Manguithre\FakerMadagascar\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class PersonGeneratorTest extends TestCase
{
    private PersonGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->generator = new PersonGenerator();
    }

    #[Test]
    public function it_generates_a_first_name(): void
    {
        $name = $this->generator->firstName();

        $this->assertNotEmpty($name);
        $this->assertGreaterThanOrEqual(3, strlen($name));
    }

    #[Test]
    public function it_generates_a_last_name(): void
    {
        $name = $this->generator->lastName();

        $this->assertNotEmpty($name);
        $this->assertGreaterThanOrEqual(3, strlen($name));
    }

    #[Test]
    public function it_generates_a_full_name(): void
    {
        $name = $this->generator->fullName();

        $this->assertNotEmpty($name);
        $this->assertStringContainsString(' ', $name);
    }

    #[Test]
    public function it_generates_distinct_names_across_calls(): void
    {
        $names = [];

        for ($i = 0; $i < 20; $i++) {
            $names[] = $this->generator->fullName();
        }

        $this->assertGreaterThan(1, count(array_unique($names)), 'Expected some variety across 20 full names');
    }
}
