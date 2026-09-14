# Faker Madagascar

[![Tests](https://github.com/manguithre/faker-madagascar/actions/workflows/tests.yml/badge.svg)](https://github.com/manguithre/faker-madagascar/actions)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.2-blue.svg)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/laravel-10%7C11%7C12%7C13-red.svg)](https://laravel.com)

A Laravel package for generating localized fake data for Madagascar, inspired by FakerPHP.

## Installation

```bash
composer require manguithre/faker-madagascar
```

## Usage

### Via Helper (recommended)

Like Laravel's `fake()` helper, the package ships an `fakerMg()` helper:

```php
fakerMg()->address();
fakerMg()->region();

// Person
fakerMg()->firstName();
fakerMg()->lastName();
fakerMg()->fullName();

// Contact
fakerMg()->phoneNumber();
fakerMg()->phoneNumber('telma');   // 034 / 038 only
fakerMg()->cin();

// FakerPHP bridge: `fake()` gives you BOTH standard FakerPHP data
// AND the malagasy* methods in the same call chain:
fake()->malagasyFullName();       // Malagasy data
fake()->name();                   // standard FakerPHP data
```

Works in routes, seeders, factories, tinker — anywhere `fake()` works. Inside a Laravel app it resolves the container singleton (config applies); in plain PHP projects it falls back to a standalone instance.

### Via Facade

```php
use FakerMg;

FakerMg::address(); // same methods as the helper
```

### Via Injection

```php
use Manguithre\FakerMadagascar\FakerMadagascar;

public function run(FakerMadagascar $faker): void
{
    Employee::factory()->count(50)->create([
        'address' => (string) $faker->address(),
        'name' => $faker->fullName(),
        'phone' => $faker->phoneNumber(),
        'cin' => $faker->cin(),
    ]);
}
```

### Via FakerPHP

The package auto-registers a provider on Laravel's Faker generator,
so all `malagasy*` methods are available in factories and seeders:

```php
$faker->malagasyAddress();       // MalagasyAddress DTO
$faker->malagasyRegion();
$faker->malagasyDistrict('ANALAMANGA');
$faker->malagasyFullName();
$faker->malagasyPhoneNumber();
$faker->malagasyCin();
```

### Address

```php
// Get a full address DTO
$address = fakerMg()->address();

echo $address->region;        // e.g. ANALAMANGA
echo $address->district;      // e.g. Antananarivo Atsimondrano
echo $address->commune;       // e.g. Alakamisy Fenoarivo
echo $address->fokontany;     // e.g. Ankadivory

echo (string) $address;       // Alakamisy Fenoarivo, Antananarivo Atsimondrano, ANALAMANGA, Ankadivory

// Force a specific region (children are consistent)
$address = fakerMg()->address(region: 'Atsinanana');

// Force district or commune
fakerMg()->addressInDistrict('Analamanga', 'Antananarivo Atsimondrano');
fakerMg()->addressInCommune('Analamanga', 'Antananarivo Atsimondrano', 'Alakamisy Fenoarivo');
```

Region names are case-insensitive: `'Analamanga'`, `'analamanga'` and `'ANALAMANGA'` all resolve to `ANALAMANGA`.

### Person

```php
fakerMg()->firstName(); // e.g. Andrata
fakerMg()->lastName();  // e.g. Randramanana
fakerMg()->fullName();  // e.g. Jarinala Rabeantoandro
```

### Contact

```php
// Phone number with verified Malagasy mobile prefixes
// 032/037 (Orange), 033/035 (Airtel), 034/038 (Telma/Yas), 039 (bip)
fakerMg()->phoneNumber(); // e.g. 0321234567

// Restrict to one operator
fakerMg()->phoneNumber('airtel'); // 033 / 035 only

// List the verified prefixes
fakerMg()->phonePrefixes(); // ['032', '033', '034', '035', '037', '038', '039']

// CIN (Carte d'Identité Nationale)
// Format: BUREAU_YEAR_SEQUENCE (8 digits) or BUREAU-YEAR-SEQUENCE (separated)
config(['faker-madagascar.cin_format' => 'separated']);
fakerMg()->cin(); // e.g. 01-24-0001
```

Prefixes are verified against the [ARTEC national numbering plan](https://www.artec.mg/plan-national-de-numerotation/),
ITU/Wikipedia numbering tables and Airtel Madagascar's June 2026 announcement of the 035 prefix.

### Validation Rules

```php
use Manguithre\FakerMadagascar\Rules\MalagasyPhoneNumber;
use Manguithre\FakerMadagascar\Rules\MalagasyCin;

$request->validate([
    'telephone' => ['required', new MalagasyPhoneNumber()],
    'cin' => ['required', new MalagasyCin()],
]);
```

The rules accept separators and the +261 country code (e.g. `+261 32 12 345 67`).

### Artisan Command

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

### Seeder Example

```php
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        Employee::factory()
            ->count(100)
            ->create([
                'first_name' => fakerMg()->firstName(),
                'last_name' => fakerMg()->lastName(),
                'address' => (string) fakerMg()->address(region: 'Analamanga'),
                'phone' => fakerMg()->phoneNumber(),
                'cin' => fakerMg()->cin(),
            ]);
    }
}
```

## Configuration

The package publishes a config file at `config/faker-madagascar.php`:

```php
return [
    'active_regions' => [],
    'cin_format' => 'auto',
];
```

### active_regions

Restrict generated data to specific regions. Leave empty for all regions.
Names are case-insensitive.

When set, forcing a region outside this list — e.g. `fakerMg()->address(region: 'X')` —
throws `Manguithre\FakerMadagascar\Exceptions\InvalidArgumentException`.

### cin_format

Choose CIN format: `compact` (8 digits), `separated` (XX-XX-XXXX) or `auto` (random per call).
Any other value throws `InvalidArgumentException`.

## Testing

```bash
composer test
```

## Credits

Geography data (23 regions, 119 districts, 1704 communes, 19 328 fokontany) sourced from
[julkwel/madagascar-map](https://github.com/julkwel/madagascar-map) (MIT License).
Rebuild the data set with `php build-geography.php` after updating the source files.

## License

MIT License. See [LICENSE](LICENSE) for details.
