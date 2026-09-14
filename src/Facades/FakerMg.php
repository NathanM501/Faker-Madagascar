<?php

declare(strict_types=1);

namespace Manguithre\FakerMadagascar\Facades;

use Illuminate\Support\Facades\Facade;

class FakerMg extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Manguithre\FakerMadagascar\FakerMadagascar::class;
    }
}
