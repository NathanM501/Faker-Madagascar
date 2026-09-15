<?php

declare(strict_types=1);

namespace Manguithre\FakerMadagascar\Tests\Unit;

use Manguithre\FakerMadagascar\Data\NameRepository;
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
    public function it_generates_a_first_name_from_the_repository(): void
    {
        $name = $this->generator->firstName();

        $this->assertNotEmpty($name);
        $this->assertContains($name, NameRepository::firstNames());
    }

    #[Test]
    public function it_generates_a_last_name_from_the_repository(): void
    {
        $name = $this->generator->lastName();

        $this->assertNotEmpty($name);
        $this->assertContains($name, NameRepository::lastNames());
    }

    #[Test]
    public function it_generates_a_full_name_combining_first_and_last(): void
    {
        $name = $this->generator->fullName();

        $this->assertStringContainsString(' ', $name);

        [$firstName, $lastName] = explode(' ', $name, 2);

        $this->assertContains($firstName, NameRepository::firstNames());
        $this->assertContains($lastName, NameRepository::lastNames());
    }

    #[Test]
    public function it_generates_distinct_names_across_calls(): void
    {
        $names = [];

        for ($i = 0; $i < 20; ++$i) {
            $names[] = $this->generator->fullName();
        }

        $this->assertGreaterThan(1, count(array_unique($names)), 'Expected some variety across 20 full names');
    }

    #[Test]
    public function repository_exposes_a_sizeable_pool_of_unique_names(): void
    {
        $firstNames = NameRepository::firstNames();
        $lastNames = NameRepository::lastNames();

        $this->assertSame(count($firstNames), count(array_unique($firstNames)), 'First names should be unique');
        $this->assertSame(count($lastNames), count(array_unique($lastNames)), 'Last names should be unique');

        $this->assertGreaterThanOrEqual(50, count($firstNames));
        $this->assertGreaterThanOrEqual(50, count($lastNames));
    }
}
