<?php

declare(strict_types=1);

namespace Manguithre\FakerMadagascar\Support;

/**
 * Laravel-optional config access.
 *
 * The package works both inside a booted Laravel application (where config
 * comes from config/faker-madagascar.php) and standalone in plain PHP
 * (where it falls back to the given defaults). Calling Laravel's global
 * config() helper directly would fatal outside a booted app.
 */
final class Config
{
    /**
     * Get a faker-madagascar config value, or the default outside Laravel.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        if (!function_exists('config')) {
            return $default;
        }

        try {
            return config("faker-madagascar.$key", $default);
        } catch (\Throwable) {
            // Container present but no 'config' binding (no booted Laravel app).
            return $default;
        }
    }
}
