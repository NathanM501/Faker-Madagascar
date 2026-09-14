<?php

declare(strict_types=1);

namespace Manguithre\FakerMadagascar\Tests\Unit;

use Manguithre\FakerMadagascar\Data\PhonePrefixRepository;
use Manguithre\FakerMadagascar\Generators\ContactGenerator;
use Manguithre\FakerMadagascar\Rules\MalagasyPhoneNumber;
use Manguithre\FakerMadagascar\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class ContactGeneratorTest extends TestCase
{
    private ContactGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->generator = new ContactGenerator();
    }

    #[Test]
    public function it_generates_a_phone_number_matching_the_verified_prefixes(): void
    {
        $phone = $this->generator->phoneNumber();

        $this->assertSame(10, strlen($phone));
        $this->assertTrue(PhonePrefixRepository::isValidPrefix($phone), "Invalid prefix generated: $phone");

        $errors = [];
        (new MalagasyPhoneNumber)->validate('phone', $phone, function (string $m) use (&$errors): void { $errors[] = $m; });

        $this->assertSame([], $errors, "Generated phone failed the validation rule: $phone");
    }

    #[Test]
    public function it_generates_phone_numbers_for_a_specific_operator(): void
    {
        foreach (PhonePrefixRepository::operators() as $operator) {
            $phone = $this->generator->phoneNumber($operator);

            $this->assertContains(substr($phone, 0, 3), PhonePrefixRepository::forOperator($operator));
        }
    }

    #[Test]
    public function it_throws_for_unknown_operator(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->generator->phoneNumber('unknown');
    }

    #[Test]
    public function it_generates_a_valid_cin_compact(): void
    {
        config(['faker-madagascar.cin_format' => 'compact']);

        $cin = $this->generator->cin();

        $this->assertMatchesRegularExpression('/^[0-9]{8}$/', $cin);
    }

    #[Test]
    public function it_generates_a_valid_cin_separated(): void
    {
        config(['faker-madagascar.cin_format' => 'separated']);

        $cin = $this->generator->cin();

        $this->assertMatchesRegularExpression('/^[0-9]{2}-[0-9]{2}-[0-9]{4}$/', $cin);
    }

    #[Test]
    public function it_throws_for_invalid_cin_format_config(): void
    {
        config(['faker-madagascar.cin_format' => 'bogus']);

        $this->expectException(\InvalidArgumentException::class);

        $this->generator->cin();
    }
}
