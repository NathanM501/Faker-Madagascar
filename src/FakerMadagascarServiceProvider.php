<?php

declare(strict_types=1);

namespace Manguithre\FakerMadagascar;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Support\ServiceProvider;
use Manguithre\FakerMadagascar\Console\PreviewCommand;
use Manguithre\FakerMadagascar\Data\GeographyRepository;
use Manguithre\FakerMadagascar\Faker\MalagasyProvider;
use Manguithre\FakerMadagascar\Generators\AddressGenerator;
use Manguithre\FakerMadagascar\Generators\ContactGenerator;
use Manguithre\FakerMadagascar\Generators\PersonGenerator;

class FakerMadagascarServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/faker-madagascar.php',
            'faker-madagascar'
        );

        // Optional FakerPHP bridge: only wired when fakerphp/faker is
        // installed. Binds a shared Faker generator if the app doesn't
        // already have one, then auto-registers the Malagasy provider on it.
        // The extender runs at resolution time, so it also applies when
        // Laravel binds its own generator.
        if (class_exists(Factory::class) && class_exists(Generator::class)) {
            if (! $this->app->bound(Generator::class)) {
                $this->app->singleton(Generator::class, fn () => Factory::create());
            }

            $this->app->extend(Generator::class, function (Generator $faker, $app) {
                $faker->addProvider($app->make(MalagasyProvider::class));

                return $faker;
            });
        }

        $this->app->singleton(GeographyRepository::class);

        $this->app->singleton(AddressGenerator::class);
        $this->app->singleton(PersonGenerator::class);
        $this->app->singleton(ContactGenerator::class);

        $this->app->singleton(FakerMadagascar::class);

        $this->app->singleton(MalagasyProvider::class);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([PreviewCommand::class]);
        }

        $this->publishes([
            __DIR__ . '/../config/faker-madagascar.php' => config_path('faker-madagascar.php'),
        ], 'config');
    }
}
