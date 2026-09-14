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
        // installed. Laravel's fake() helper binds its generators under
        // suffixed keys (Faker\Generator:en_US, :fr_FR, ...) and does NOT
        // use a Faker\Generator binding, so a plain extend(Generator::class)
        // never sees it. A global afterResolving hook decorates every Faker
        // Generator the container resolves, under any key. The idempotence
        // guard prevents attaching the provider twice to the same generator.
        if (class_exists(Factory::class) && class_exists(Generator::class)) {
            $this->app->afterResolving(function ($object, $app) {
                if ($object instanceof Generator) {
                    if (! in_array($app->make(MalagasyProvider::class), $object->getProviders(), true)) {
                        $object->addProvider($app->make(MalagasyProvider::class));
                    }
                }
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
        ], 'faker-madagascar-config');
    }
}
