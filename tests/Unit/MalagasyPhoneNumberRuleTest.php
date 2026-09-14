<?php

declare(strict_types=1);

namespace Manguithre\FakerMadagascar\Tests\Unit;

use Manguithre\FakerMadagascar\Data\PhonePrefixRepository;
use Manguithre\FakerMadagascar\Rules\MalagasyPhoneNumber;
use Manguithre\FakerMadagascar\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class MalagasyPhoneNumberRuleTest extends TestCase
{
    private MalagasyPhoneNumber $rule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rule = new MalagasyPhoneNumber();
    }

    #[Test]
    public function it_passes_for_valid_number(): void
    {
        $this->assertRulePasses('0321234567');
    }

    #[Test]
    public function it_passes_for_all_verified_prefixes(): void
    {
        foreach (PhonePrefixRepository::all() as $prefix) {
            $this->assertRulePasses($prefix . '1234567', "Failed for prefix $prefix");
        }
    }

    #[Test]
    public function it_accepts_separators_and_country_code(): void
    {
        $this->assertRulePasses('032 12 345 67');
        $this->assertRulePasses('+261 32 12 345 67');
    }

    #[Test]
    public function it_fails_for_invalid_prefix(): void
    {
        $this->assertRuleFails('0311234567');
        $this->assertRuleFails('0361234567');
    }

    #[Test]
    public function it_fails_for_non_string(): void
    {
        $this->assertRuleFails(1234567890);
    }

    #[Test]
    public function it_fails_for_invalid_length(): void
    {
        $this->assertRuleFails('032123456');
        $this->assertRuleFails('03212345678');
    }

    private function assertRulePasses(mixed $value, string $message = ''): void
    {
        $errors = [];

        ($this->rule->validate('phone', $value, function (string $m) use (&$errors): void {
            $errors[] = $m;
        }));

        $this->assertSame([], $errors, $message);
    }

    private function assertRuleFails(mixed $value, string $message = ''): void
    {
        $errors = [];

        ($this->rule->validate('phone', $value, function (string $m) use (&$errors): void {
            $errors[] = $m;
        }));

        $this->assertNotEmpty($errors, $message);
    }
}
