# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- **`fakerMg()` helper** (inspired by Laravel's `fake()`): `fakerMg()->address()`, `fakerMg()->phoneNumber()`, etc. Resolves the container singleton inside Laravel; falls back to a standalone instance in plain PHP. `composer.json` now autoloads `src/helpers.php` under `files`.
- **Laravel 13 support** (`illuminate/*` `^13.0`, `orchestra/testbench` `^11.0`, Pest `^3|^4|^5`).
- `MalagasyPhoneNumber` rule now accepts the `+261` country code (e.g. `+261 32 12 345 67`), as documented.

### Changed
- **Name generation**: `firstName()` and `lastName()` now return real Malagasy names from curated data sets (102 first names, 103 last names) instead of syllable combinations, producing more realistic and recognizable Malagasy names.
- **Laravel 11+ only**: Laravel 10 support has been dropped (`illuminate/*` `^11.0|^12.0|^13.0`, `orchestra/testbench` `^9|^10|^11`). The CI matrix is now PHP 8.2/8.3/8.4 × Laravel 11/12/13, and the `FakerMg` alias works everywhere out of the box (global aliases exist since Laravel 11).
- **Real fokontany data**: the 1 704 synthetic `FOKONTANY-*` placeholders were replaced by the **20 688 real fokontany** from [julkwel/madagascar-map](https://github.com/julkwel/madagascar-map). Every generated address is now fully real down to the fokontany level. Rebuild with `php build-geography.php`.
- **`fakerphp/faker` is now optional** (`suggest` instead of `require`): the core generators no longer depend on it. The `fake()->malagasy*()` bridge auto-wires only when FakerPHP is installed.
- `AddressGenerator` and `ContactGenerator` no longer depend on FakerPHP internally; `GeographyRepository` is now an injectable instance instead of a static-only class.
- `fakermg:preview --type=person` now outputs first name, last name and full name.
- Exports (`--export=json|csv`) are written to the system temp directory instead of the current working directory.

### Fixed
- **PreviewCommand**: `fakermg:preview --type=person` now uses the same first and last name for the full name display instead of generating three independent random names.
- **Real CIN format**: generated and validated CIN numbers now match the real Malagasy CIN — exactly **12 digits with no separators** (`XXXXXXXXXXXX`). The previous model (8 digits, bureau/year/sequence, optional `01-02-0003` separator format) was incorrect and is removed, along with the `cin_format` config option. The `MalagasyCin` rule now requires exactly 12 digits but tolerates separators typed by users.
- The package no longer fatals outside a booted Laravel app: config access goes through `Support\Config`, which falls back to defaults when no Laravel `config` repository is available (plain PHP usage of `fakerMg()` was broken before).
- Phone prefixes now come from a single verified source (`PhonePrefixRepository`): 032/037 (Orange), 033/035 (Airtel), 034/038 (Telma/Yas), 039 (bip). The generator and the `MalagasyPhoneNumber` rule can no longer disagree — previously the package generated `035`/`036`/`039` numbers that its own rule rejected.
- The `fake()->malagasy*()` bridge now works in real apps: Laravel binds its Faker generators under locale-suffixed keys (e.g. `Faker\Generator:en_US`), which the previous `extend()` never matched. A global `afterResolving` hook now decorates every `Faker\Generator` resolved from the container, whatever its key or locale.
- Config publishing now uses the dedicated `faker-madagascar-config` tag; the previous generic `config` tag made `vendor:publish` unreliable and could collide with other packages.

### Breaking
- **CIN model change**: `cin()` now generates 12-digit numbers and the `cin_format` config option no longer exists.
- Laravel 10 is no longer supported (minimum Laravel 11).
- `ContactGenerator` constructor takes no arguments (was `ContactGenerator(Generator $faker)`).
- `GeographyRepository` methods are instance methods (were static); resolve the class from the container.
