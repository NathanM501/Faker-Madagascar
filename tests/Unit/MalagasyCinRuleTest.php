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

        $this->rule = new MalagasyCin;
    }

    #[Test]
    public function it_passes_for_valid_compact_cin(): void
    {
        $this->assertRulePasses('01010001');
    }

    #[Test]
    public function it_passes_for_separated_format(): void
    {
        $this->assertRulePasses('01-01-0001');
    }

    #[Test]
    public function it_fails_for_invalid_bureau(): void
    {
        // 8 digits total, bureau '00' is out of the 1-22 range.
        $this->assertRuleFails('00010001', 'Bureau 00 must be rejected');
        $this->assertRuleFails('99010001', 'Bureau 99 must be rejected');
    }

    #[Test]
    public function it_fails_for_invalid_length(): void
    {
        $this->assertRuleFails('0101000', '7 digits must be rejected');
        $this->assertRuleFails('010100011', '9 digits must be rejected');
    }

    #[Test]
    public function it_fails_for_non_string(): void
    {
        $this->assertRuleFails(1010001);
    }

    private function assertRulePasses(mixed $value, string $message = ''): void
    {
        $errors = [];

        ($this->rule->validate('cin', $value, function (string $m) use (&$errors): void { $errors[] = $m; }));

        $this->assertSame([], $errors, $message);
    }

    private function assertRuleFails(mixed $value, string $message = ''): void
    {
        $errors = [];

        ($this->rule->validate('cin', $value, function (string $m) use (&$errors): void { $errors[] = $m; }));

        $this->assertNotEmpty($errors, $message);
    }
}
