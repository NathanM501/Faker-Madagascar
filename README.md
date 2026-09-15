# Faker Madagascar

[![Tests](https://github.com/NathanM501/Faker-Madagascar/actions/workflows/tests.yml/badge.svg)](https://github.com/NathanM501/Faker-Madagascar/actions/workflows/tests.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/manguithre/faker-madagascar.svg?style=flat-square)](https://packagist.org/packages/manguithre/faker-madagascar)
[![Total Downloads](https://img.shields.io/packagist/dt/manguithre/faker-madagascar.svg?style=flat-square)](https://packagist.org/packages/manguithre/faker-madagascar)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.2-blue.svg)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/laravel-11%20%7C%2012%20%7C%2013-red.svg)](https://laravel.com)

Generate realistic Madagascar-specific test data for Laravel and PHP
applications.

Faker Madagascar provides Malagasy names, structured geographical addresses,
operator-aware mobile phone numbers and CIN-formatted values.

The package includes:

- 23 regions.
- 119 districts.
- 1,704 communes.
- 20,688 fokontany names.

If you already know FakerPHP's `fake()` helper, you can start using this
package immediately.

> Generated data is intended for development, testing and database seeding.
> It does not represent real individuals or officially issued documents.

## Features

- Malagasy first names, last names and full names.
- Hierarchical addresses: region, district, commune and fokontany.
- 23 regions, 119 districts, 1,704 communes and 20,688 fokontany names.
- Malagasy mobile numbers using supported operator prefixes.
- CIN-formatted values with 12 digits.
- Laravel validation rules for phone numbers and CIN values.
- Laravel factories and seeders integration.
- FakerPHP integration through `fake()->malagasy*()`.
- Artisan preview command.
- JSON and CSV export.
- Region filtering through configuration.
- Standalone PHP support without Laravel.
- Case-insensitive region lookup.

## Requirements

- PHP 8.2 or higher.
- Laravel 11, 12 or 13.
- FakerPHP `^1.21` only if you want to use the
  `fake()->malagasy*()` integration.

In a standard Laravel application, FakerPHP is usually already installed.

## Installation

Install the package with Composer:

```bash
composer require manguithre/faker-madagascar
```

The service provider and the `FakerMg` facade alias are registered through
Laravel package discovery. No manual registration is required.

The `fakerMg()` helper is available immediately after installation.

Laravel supports package auto-discovery through the package's Composer
configuration, allowing service providers and facade aliases to be registered
automatically. [16]

## Quick start

```php
$address = fakerMg()->address();

echo $address;
// Alakamisy Fenoarivo, Antananarivo Atsimondrano, ANALAMANGA, Ankadivory

echo fakerMg()->fullName();
// Jarinala Rabeantoandro

echo fakerMg()->phoneNumber();
// 0321234567

echo fakerMg()->cin();
// 123456789012
```

## Usage styles

All usage styles resolve to the same Faker Madagascar service inside Laravel.

### Using the `fakerMg()` helper

The helper is the recommended usage style:

```php
fakerMg()->address();

fakerMg()->region();
// ANALAMANGA

fakerMg()->firstName();
// Andrata

fakerMg()->lastName();
// Randramanana

fakerMg()->fullName();
// Jarinala Rabeantoandro

fakerMg()->phoneNumber();
// 0321234567

fakerMg()->phoneNumber('telma');
// 0341234567

fakerMg()->cin();
// 123456789012
```

The helper works in:

- Routes.
- Controllers.
- Models and factories.
- Seeders.
- Artisan commands.
- Tinker.
- Console applications.

### Using the facade

```php
FakerMg::address();

FakerMg::phoneNumber();

FakerMg::fullName();
```

If your project requires an explicit facade import, use the namespace exposed
by the package:

```php
use Manguithre\FakerMadagascar\Facades\FakerMg;
```

### Using dependency injection

```php
use Manguithre\FakerMadagascar\FakerMadagascar;

public function run(FakerMadagascar $faker): void
{
    $address = $faker->address(region: 'Atsinanana');

    dump($address);
}
```

### Using FakerPHP

If FakerPHP is installed, the package registers a provider on Laravel's Faker
generator. This allows you to mix standard FakerPHP data with Madagascar-specific
data in the same call chain:

```php
fake()->name();

fake()->malagasyFullName();

fake()->malagasyAddress();

fake()->malagasyPhoneNumber();

fake()->malagasyCin();
```

> **Note**: this bridge only works when Faker's Generator is resolved from the Laravel container (via `fake()`). Using `Faker\Factory::create()` directly won't attach the provider — use `app(Faker\Generator::class)` or the `fake()` helper instead.

## API reference

| Method                         | Description                                      | Return value      |
| ------------------------------ | ------------------------------------------------ | ----------------- |
| `address()`                    | Generates a random hierarchical address.         | `MalagasyAddress` |
| `address(region: ...)`         | Generates an address inside a specific region.   | `MalagasyAddress` |
| `addressInDistrict(...)`       | Generates an address inside a specific district. | `MalagasyAddress` |
| `addressInCommune(...)`        | Generates an address inside a specific commune.  | `MalagasyAddress` |
| `region()`                     | Returns a random region.                         | `string`          |
| `districts($region)`           | Lists the districts of a region.                 | `array`           |
| `district($region)`            | Returns a random district in a region.           | `string`          |
| `communes($region, $district)` | Lists the communes of a district.                | `array`           |
| `commune($region, $district)`  | Returns a random commune in a district.          | `string`          |
| `fokontany(...)`               | Returns a random fokontany.                      | `string`          |
| `firstName()`                  | Generates a Malagasy first name.                 | `string`          |
| `lastName()`                   | Generates a Malagasy last name.                  | `string`          |
| `fullName()`                   | Generates a Malagasy full name.                  | `string`          |
| `phoneNumber()`                | Generates a Malagasy mobile number.              | `string`          |
| `phonePrefixes()`              | Returns the supported mobile prefixes.           | `array`           |
| `cin()`                        | Generates a 12-digit CIN-formatted value.        | `string`          |

> Return types shown above should match the actual public API of the installed
> package version.

## Generating addresses

### Random address

```php
$address = fakerMg()->address();

echo $address->region;
// ANALAMANGA

echo $address->district;
// Antananarivo Atsimondrano

echo $address->commune;
// Alakamisy Fenoarivo

echo $address->fokontany;
// Ankadivory
```

The address object can be converted to a string:

```php
echo (string) $address;

// Alakamisy Fenoarivo, Antananarivo Atsimondrano, ANALAMANGA, Ankadivory
```

It can also be converted to an array:

```php
print_r($address->toArray());
```

### Force a region

```php
$address = fakerMg()->address(region: 'Atsinanana');
```

The district, commune and fokontany are selected consistently from the
specified region.

Region names are case-insensitive:

```php
fakerMg()->address(region: 'Analamanga');

fakerMg()->address(region: 'analamanga');

fakerMg()->address(region: 'ANALAMANGA');
```

These values refer to the same region.

### Force a district

```php
$address = fakerMg()->addressInDistrict(
    'ANALAMANGA',
    'Antananarivo Atsimondrano'
);
```

### Force a commune

```php
$address = fakerMg()->addressInCommune(
    'ANALAMANGA',
    'Antananarivo Atsimondrano',
    'Alakamisy Fenoarivo'
);
```

Region names are resolved case-insensitively. District and commune names must
match the names available in the dataset.

For example:

```php
'Antananarivo Atsimondrano'
```

is valid, while this value may not be valid if exact matching is required:

```php
'antananarivo atsimondrano'
```

Unknown regions, districts or communes throw:

```php
Manguithre\FakerMadagascar\Exceptions\InvalidArgumentException
```

### Browse the geographical hierarchy

```php
fakerMg()->region();
// ANALAMANGA

fakerMg()->districts('ANALAMANGA');
// List of districts

fakerMg()->district('ANALAMANGA');
// One random district

fakerMg()->communes(
    'ANALAMANGA',
    'Antananarivo Atsimondrano'
);
// List of communes

fakerMg()->commune(
    'ANALAMANGA',
    'Antananarivo Atsimondrano'
);
// One random commune

fakerMg()->fokontany(
    'ANALAMANGA',
    'Antananarivo Atsimondrano',
    'Alakamisy Fenoarivo'
);
// One random fokontany
```

## Generating names

```php
fakerMg()->firstName();
// Andrata

fakerMg()->lastName();
// Randramanana

fakerMg()->fullName();
// Jarinala Rabeantoandro
```

Names are generated from curated Malagasy name lists. They are intended
to look natural for testing and development purposes.

Generated names are not guaranteed to correspond to real people.

## Generating contact data

### Phone numbers

```php
fakerMg()->phoneNumber();
// 0321234567
```

You can optionally select an operator:

```php
fakerMg()->phoneNumber('airtel');
// 0331234567 or 0351234567

fakerMg()->phoneNumber('telma');
// 0341234567 or 0381234567
```

Supported prefixes:

```php
fakerMg()->phonePrefixes();

// ['032', '033', '034', '035', '037', '038', '039']
```

The currently supported prefixes are:

| Prefixes     | Operator    |
| ------------ | ----------- |
| `032`, `037` | Orange      |
| `033`, `035` | Airtel      |
| `034`, `038` | Telma / Yas |
| `039`        | Bip         |

Phone prefixes were checked against the ARTEC national numbering plan,
international numbering tables and available operator announcements. The
prefix dataset may need to be updated when the Malagasy numbering plan changes.

### CIN values

```php
fakerMg()->cin();

// 123456789012
```

The generated value contains 12 digits without separators.

> CIN generation is intended for testing only. The package does not verify
> whether a CIN was officially issued or belongs to a real person.

## Laravel factories and seeders

The package can be used directly inside Laravel factories.

```php
// database/factories/EmployeeFactory.php

use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name' => fakerMg()->firstName(),
            'last_name'  => fakerMg()->lastName(),
            'phone'      => fakerMg()->phoneNumber(),
            'cin'        => fakerMg()->cin(),
            'address'    => (string) fakerMg()->address(
                region: 'Analamanga'
            ),
        ];
    }
}
```

Create 100 employees:

```php
Employee::factory()
    ->count(100)
    ->create();
```

### Important factory note

Generate values inside the factory definition so that every row receives fresh
data.

Avoid calculating values once and reusing them:

```php
// Avoid this pattern if you want unique generated values.
$employee = [
    'first_name' => fakerMg()->firstName(),
    'last_name'  => fakerMg()->lastName(),
];

Employee::factory()
    ->count(100)
    ->create($employee);
```

The factory definition or a loop in a seeder should call `fakerMg()` for each
record.

### Seeder example

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
                'address'    => (string) fakerMg()->address(
                    region: 'Analamanga'
                ),
                'phone'      => fakerMg()->phoneNumber(),
                'cin'        => fakerMg()->cin(),
            ]);
        }
    }
}
```

## Validation rules

The package provides Laravel validation rules for Malagasy phone numbers and
CIN values.

```php
use Manguithre\FakerMadagascar\Rules\MalagasyCin;
use Manguithre\FakerMadagascar\Rules\MalagasyPhoneNumber;

$request->validate([
    'telephone' => [
        'required',
        new MalagasyPhoneNumber(),
    ],

    'cin' => [
        'required',
        new MalagasyCin(),
    ],
]);
```

### `MalagasyPhoneNumber`

The phone validation rule accepts:

```text
0321234567
032 12 345 67
+261321234567
+261 32 12 345 67
```

It validates the Malagasy mobile format and the supported operator prefixes.

### `MalagasyCin`

The CIN validation rule accepts a 12-digit Malagasy CIN format.

Separators are tolerated and stripped before validation:

```text
123456789012
1234 5678 9012
1234-5678-9012
1234.5678.9012
```

The rule validates the format only. It does not verify whether the CIN belongs
to a real person or was officially issued.

## Artisan command

The package includes an Artisan command for previewing generated data.

### Generate random addresses

```bash
php artisan fakermg:preview \
    --count=10 \
    --type=address
```

### Generate random persons

```bash
php artisan fakermg:preview \
    --count=5 \
    --type=person
```

### Generate contact data

```bash
php artisan fakermg:preview \
    --type=contact
```

### Export JSON

```bash
php artisan fakermg:preview \
    --count=10 \
    --type=address \
    --export=json
```

### Export CSV

```bash
php artisan fakermg:preview \
    --count=10 \
    --type=contact \
    --export=csv
```

Export files are written to the system temporary directory. The command
displays the generated file path after the export is completed.

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish \
    --tag=faker-madagascar-config
```

The configuration file is:

```php
// config/faker-madagascar.php

return [
    /*
    |--------------------------------------------------------------------------
    | Active regions
    |--------------------------------------------------------------------------
    |
    | Restrict random address generation to specific regions.
    | An empty array enables all 23 regions.
    |
    */

    'active_regions' => [],
];
```

### Restrict random generation to selected regions

```php
'active_regions' => [
    'Analamanga',
    'Atsinanana',
],
```

With this configuration, random address generation only uses the selected
regions.

```php
fakerMg()->address();
```

Forcing an inactive region throws an exception:

```php
fakerMg()->address(region: 'Diana');
```

Region names in the configuration are case-insensitive:

```php
'active_regions' => [
    'analamanga',
    'ATSINANANA',
],
```

## Using the package outside Laravel

The core service can also be used in a plain PHP application.

```php
<?php

require 'vendor/autoload.php';

$address = fakerMg()->address();

echo $address;
```

When Laravel is not available, the helper falls back to a standalone
`FakerMadagascar` instance.

## Testing the package in a fresh Laravel application

Create a fresh Laravel application:

```bash
composer create-project laravel/laravel demo-fakermg

cd demo-fakermg
```

If you are testing a local checkout of the package, configure the local Composer
repository:

```bash
composer config repositories.fakermg path ../Faker-Madagascar
```

Install the local development version:

```bash
composer require manguithre/faker-madagascar:@dev
```

Then add a test route in `routes/web.php`:

```php
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return [
        'address' => (string) fakerMg()->address(),
        'person'  => fakerMg()->fullName(),
        'phone'   => fakerMg()->phoneNumber(),
        'cin'     => fakerMg()->cin(),
    ];
});
```

Start the development server:

```bash
php artisan serve
```

Open the displayed local URL and visit:

```text
/test
```

## Testing

Run the test suite with:

```bash
composer test
```

You can also run the package tests directly if the project provides a
PHPUnit configuration:

```bash
vendor/bin/phpunit
```

## Data sources

Geographical data includes:

- 23 regions.
- 119 districts.
- 1,704 communes.
- 20,688 fokontany names.

The geographical dataset is sourced from
[julkwel/madagascar-map](https://github.com/julkwel/madagascar-map), released
under the MIT License.

After updating the source geography files, rebuild the generated dataset with:

```bash
php build-geography.php
```

Phone prefixes are maintained using information from the
[ARTEC national numbering plan](https://www.artec.mg/plan-national-de-numerotation/)
and other available Malagasy operator and numbering references.

## Contributing

Contributions, corrections and improvements are welcome.

Before opening a pull request:

```bash
composer install
composer test
```

When contributing geographical data, please include the source and explain the
reason for the change.

For new public methods, include:

- Tests.
- PHPDoc or README documentation.
- A usage example.
- Any relevant backward-compatibility considerations.

## Versioning

This package follows semantic versioning whenever possible.

Breaking changes may require a major version update. Check the changelog and
release notes before upgrading between major versions.

## Credits

- Geographical data from
  [julkwel/madagascar-map](https://github.com/julkwel/madagascar-map),
  released under the MIT License.
- Phone prefix references from ARTEC and Malagasy telecom resources.
- FakerPHP for the original Faker provider concept and ecosystem.

## License

This package is open-sourced software licensed under the MIT License.

See the [LICENSE](LICENSE) file for more information.
