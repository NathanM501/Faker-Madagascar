<?php

declare(strict_types=1);

namespace Manguithre\FakerMadagascar\Tests\Unit;

use Manguithre\FakerMadagascar\Rules\MalagasyCin;
use Manguithre\FakerMadagascar\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class MalagasyCinRuleTest extends TestCase
{
    private MalagasyCin $rule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rule = new MalagasyCin();
    }

    #[Test]
    public function it_passes_for_a_12_digit_cin(): void
    {
        $this->assertRulePasses('123456789012');
        $this->assertRulePasses('000000000000');
    }

    #[Test]
    public function it_tolerates_separators_typed_by_users(): void
    {
        // Users often type the number with spaces or dashes; the digits count is what matters.
        $this->assertRulePasses('12 34 56789 012');
        $this->assertRulePasses('1234-5678-9012');
    }

    #[Test]
    public function it_fails_for_invalid_length(): void
    {
        $this->assertRuleFails('12345678901', '11 digits must be rejected');
        $this->assertRuleFails('1234567890123', '13 digits must be rejected');
        $this->assertRuleFails('12345678', '8 digits (old format) must be rejected');
    }

    #[Test]
    public function it_fails_for_non_numeric_input(): void
    {
        $this->assertRuleFails('abcdefghijkl');
    }

    #[Test]
    public function it_fails_for_non_string(): void
    {
        $this->assertRuleFails(123456789012);
    }

    private function assertRulePasses(mixed $value, string $message = ''): void
    {
        $errors = [];

        ($this->rule->validate('cin', $value, function (string $m) use (&$errors): void {
            $errors[] = $m;
        }));

        $this->assertSame([], $errors, $message);
    }

    private function assertRuleFails(mixed $value, string $message = ''): void
    {
        $errors = [];

        ($this->rule->validate('cin', $value, function (string $m) use (&$errors): void {
            $errors[] = $m;
        }));

        $this->assertNotEmpty($errors, $message);
    }
}
