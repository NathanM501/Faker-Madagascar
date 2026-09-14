# Faker Madagascar

[![Tests](https://github.com/NathanM501/Faker-Madagascar/actions/workflows/tests.yml/badge.svg)](https://github.com/NathanM501/Faker-Madagascar/actions/workflows/tests.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.2-blue.svg)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/laravel-11%7C12%7C13-red.svg)](https://laravel.com)

A Laravel package for generating realistic fake data for Madagascar — addresses
(23 regions, 119 districts, 1 704 communes, 19 328 real fokontany), Malagasy
names, operator-valid phone numbers and CIN numbers. Inspired by FakerPHP:
if you know `fake()`, you already know how to use it.

## Requirements

- PHP 8.2+
- Laravel 11, 12 or 13
- Optionally [FakerPHP](https://github.com/fakerphp/faker) `^1.21` for the `fake()->malagasy*()` bridge (Composer installs it automatically if missing)

## Installation

```bash
composer require manguithre/faker-madagascar
```

The service provider and the `FakerMg` alias are auto-discovered by Laravel —
there is nothing to register manually. The `fakerMg()` helper is available
everywhere, immediately.

## Quick start

```php
use Manguithre\FakerMadagascar\FakerMadagascar;

$address = fakerMg()->address();

echo $address;              // Alakamisy Fenoarivo, Antananarivo Atsimondrano, ANALAMANGA, Ankadivory
echo fakerMg()->fullName(); // e.g. Jarinala Rabeantoandro
echo fakerMg()->phoneNumber(); // e.g. 0321234567
echo fakerMg()->cin();         // e.g. 123456789012
```

## Usage styles

Pick whichever feels natural — they all resolve to the same singleton inside Laravel.

### Via the `fakerMg()` helper (recommended)

```php
fakerMg()->address();          // MalagasyAddress DTO
fakerMg()->region();           // e.g. ANALAMANGA

// Person
fakerMg()->firstName();
fakerMg()->lastName();
fakerMg()->fullName();

// Contact
fakerMg()->phoneNumber();          // 10 digits, starts with 0
fakerMg()->phoneNumber('telma');   // 034 / 038 only
fakerMg()->cin();                  // e.g. 123456789012
```

Works in routes, controllers, seeders, factories, tinker and commands.

### Via the facade

```php
use FakerMg; // no import needed on Laravel 11+, where aliases are global

FakerMg::address();
FakerMg::phoneNumber();
```

### Via dependency injection

```php
use Manguithre\FakerMadagascar\FakerMadagascar;

public function run(FakerMadagascar $faker): void
{
    dump($faker->address(region: 'Atsinanana'));
}
```

### Via FakerPHP (`fake()->malagasy*()`)

If FakerPHP is installed, the package auto-registers a provider on Laravel's
Faker generator — mixing standard and Malagasy data in the same call chain:

```php
fake()->name();                 // standard FakerPHP data
fake()->malagasyFullName();     // Malagasy data
fake()->malagasyAddress();      // MalagasyAddress DTO
fake()->malagasyPhoneNumber();
fake()->malagasyCin();
```

## Generating data

### Address

```php
$address = fakerMg()->address();

echo $address->region;      // e.g. ANALAMANGA (region names are UPPERCASE in the data set)
echo $address->district;    // e.g. Antananarivo Atsimondrano
echo $address->commune;     // e.g. Alakamisy Fenoarivo
echo $address->fokontany;   // e.g. Ankadivory (real fokontany names)

echo (string) $address;     // "commune, district, region, fokontany"
print_r($address->toArray());

// Force a specific region — children (district, commune, fokontany) stay consistent.
// Region names are case-insensitive: 'Analamanga' == 'analamanga' == 'ANALAMANGA'.
$address = fakerMg()->address(region: 'Atsinanana');

// Force deeper levels
fakerMg()->addressInDistrict('ANALAMANGA', 'Antananarivo Atsimondrano');
fakerMg()->addressInCommune('ANALAMANGA', 'Antananarivo Atsimondrano', 'Alakamisy Fenoarivo');
```

> Region names are resolved case-insensitively. District and commune names must
> match the data set (e.g. `'Antananarivo Atsimondrano'`, not `'antananarivo atsimondrano'`).
> Unknown names throw `Manguithre\FakerMadagascar\Exceptions\InvalidArgumentException`.

Browsing the hierarchy directly:

```php
fakerMg()->region();                                  // one random region
fakerMg()->districts('ANALAMANGA');                   // list of districts
fakerMg()->district('ANALAMANGA');                    // one random district
fakerMg()->communes('ANALAMANGA', 'Antananarivo Atsimondrano');
fakerMg()->commune('ANALAMANGA', 'Antananarivo Atsimondrano');   // one random commune
fakerMg()->fokontany('ANALAMANGA', 'Antananarivo Atsimondrano', 'Alakamisy Fenoarivo');
```

### Person

```php
fakerMg()->firstName(); // e.g. Andrata
fakerMg()->lastName();  // e.g. Randramanana
fakerMg()->fullName();  // e.g. Jarinala Rabeantoandro
```

Names are built from Malagasy syllable patterns, so they look and feel Malagasy.

### Contact

```php
// Phone numbers use verified Malagasy mobile prefixes only:
// 032/037 (Orange), 033/035 (Airtel), 034/038 (Telma/Yas), 039 (bip)
fakerMg()->phoneNumber();            // e.g. 0321234567
fakerMg()->phoneNumber('airtel');    // 033 / 035 only
fakerMg()->phonePrefixes();          // ['032', '033', '034', '035', '037', '038', '039']

// CIN (Carte d'Identité Nationale): 12 digits, no separators
fakerMg()->cin();                    // e.g. 123456789012
```

Prefixes are verified against the [ARTEC national numbering plan](https://www.artec.mg/plan-national-de-numerotation/),
ITU/Wikipedia numbering tables and Airtel Madagascar's June 2026 announcement of the 035 prefix.

## Laravel factories and seeders

The classic FakerPHP pattern works — put `fakerMg()` calls in a factory
definition so **every row gets fresh values**:

```php
// database/factories/EmployeeFactory.php
class EmployeeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name' => fakerMg()->firstName(),
            'last_name'  => fakerMg()->lastName(),
            'phone'      => fakerMg()->phoneNumber(),
            'cin'        => fakerMg()->cin(),
            'address'    => (string) fakerMg()->address(region: 'Analamanga'),
        ];
    }
}

// Anywhere:
Employee::factory()->count(100)->create();
```

⚠️ Don't compute the values once and reuse them inside `->create([...])` —
that would give every row the same data. Call `fakerMg()` per row (factory
definitions, or a loop in a seeder).

Seeder with a loop:

```php
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 0; $i < 100; $i++) {
            Employee::create([
                'first_name' => fakerMg()->firstName(),
                'last_name'  => fakerMg()->lastName(),
                'address'    => (string) fakerMg()->address(region: 'Analamanga'),
                'phone'      => fakerMg()->phoneNumber(),
                'cin'        => fakerMg()->cin(),
            ]);
        }
    }
}
```

## Validation rules

```php
use Manguithre\FakerMadagascar\Rules\MalagasyPhoneNumber;
use Manguithre\FakerMadagascar\Rules\MalagasyCin;

$request->validate([
    'telephone' => ['required', new MalagasyPhoneNumber()],
    'cin'       => ['required', new MalagasyCin()],
]);
```

- `MalagasyPhoneNumber` accepts separators and the +261 country code
  (e.g. `+261 32 12 345 67`, `032 12 345 67`) and validates the operator prefix.
- `MalagasyCin` validates the real 12-digit CIN format (separators typed by
  users — spaces, dashes, dots — are tolerated and stripped).

## Artisan command

```bash
# Generate 10 random addresses
php artisan fakermg:preview --count=10 --type=address

# Generate 5 random persons
php artisan fakermg:preview --count=5 --type=person

# Generate contact data
php artisan fakermg:preview --type=contact

# Export to JSON / CSV (written to the system temp directory)
php artisan fakermg:preview --count=10 --type=address --export=json
php artisan fakermg:preview --count=10 --type=contact --export=csv
```

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag=faker-madagascar-config
```

```php
// config/faker-madagascar.php
return [
    // Restrict generation to specific regions. Empty = all 23 regions.
    'active_regions' => [],
];
```

- With `active_regions` set (e.g. `['Analamanga', 'Atsinanana']`), random generation
  only picks those regions, and forcing an inactive region
  (`fakerMg()->address(region: 'Diana')`) throws
  `Manguithre\FakerMadagascar\Exceptions\InvalidArgumentException`.
- Region names in the config are case-insensitive.

## Using the package outside Laravel

The core works in plain PHP too — the helper falls back to a standalone instance:

```php
require 'vendor/autoload.php';

$address = fakerMg()->address(); // works, no Laravel needed
```

## Testing this package in a fresh Laravel app

```bash
composer create-project laravel/laravel demo-fakermg
cd demo-fakermg

composer config repositories.fakermg path ../Faker-Madagascar
composer require manguithre/faker-madagascar:@dev
```

Then in `routes/web.php`:

```php
Route::get('/test', fn () => [
    'address' => (string) fakerMg()->address(),
    'person'  => fakerMg()->fullName(),
    'phone'   => fakerMg()->phoneNumber(),
    'cin'     => fakerMg()->cin(),
]);
```

## Testing

```bash
composer test
```

## Credits

- Geography data (23 regions, 119 districts, 1 704 communes, 19 328 fokontany)
  from [julkwel/madagascar-map](https://github.com/julkwel/madagascar-map) (MIT License).
  Rebuild the data set with `php build-geography.php` after updating the source files.
- Phone prefixes verified against ARTEC, the Malagasy telecom regulator.

## License

MIT License. See [LICENSE](LICENSE) for details.
