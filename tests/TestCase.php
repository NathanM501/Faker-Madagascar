<?php

declare(strict_types=1);

namespace Manguithre\FakerMadagascar\Tests;

use Manguithre\FakerMadagascar\FakerMadagascarServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            FakerMadagascarServiceProvider::class,
        ];
    }
}
