<?php

declare(strict_types=1);

namespace Manguithre\FakerMadagascar\Tests\Unit;

use Manguithre\FakerMadagascar\Support\Config;
use Manguithre\FakerMadagascar\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class ConfigTest extends TestCase
{
    #[Test]
    public function it_returns_default_when_key_does_not_exist(): void
    {
        $this->assertNull(Config::get('nonexistent.key'));
    }

    #[Test]
    public function it_returns_given_default_value(): void
    {
        $this->assertSame(['fallback'], Config::get('missing.key', ['fallback']));
    }

    #[Test]
    public function it_returns_configured_value_in_laravel(): void
    {
        config(['faker-madagascar.active_regions' => ['ANALAMANGA']]);

        $this->assertSame(['ANALAMANGA'], Config::get('active_regions'));
    }
}
