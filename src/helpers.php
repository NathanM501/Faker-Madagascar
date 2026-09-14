<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| fakerMg() helper
|--------------------------------------------------------------------------
|
| Inspired by Laravel's fake() helper: a short, ergonomic entry point.
| Inside a Laravel app it resolves the FakerMadagascar singleton (so the
| config, the published config file and the container bindings all apply);
| outside Laravel it falls back to a standalone instance.
|
|     fakerMg()->address();
|     fakerMg()->phoneNumber('telma');
|     fakerMg()->fullName();
|
*/

use Manguithre\FakerMadagascar\FakerMadagascar;
use Manguithre\FakerMadagascar\Generators\AddressGenerator;
use Manguithre\FakerMadagascar\Generators\ContactGenerator;
use Manguithre\FakerMadagascar\Generators\PersonGenerator;

if (!function_exists('fakerMg')) {
    /**
     * Get a FakerMadagascar instance.
     *
     * Resolves the container singleton when a Laravel/Lumen container is
     * available; builds a standalone instance otherwise (plain PHP projects).
     */
    function fakerMg(): FakerMadagascar
    {
        $container = null;

        if (function_exists('app')) {
            $container = app();
        } elseif (class_exists(\Illuminate\Container\Container::class)) {
            $container = \Illuminate\Container\Container::getInstance();
        }

        if ($container instanceof \Psr\Container\ContainerInterface && $container->bound(FakerMadagascar::class)) {
            return $container->make(FakerMadagascar::class);
        }

        return new FakerMadagascar(
            new AddressGenerator(),
            new PersonGenerator(),
            new ContactGenerator(),
        );
    }
}
