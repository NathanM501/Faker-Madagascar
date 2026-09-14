# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- **Real fokontany data**: the 1 704 synthetic `FOKONTANY-*` placeholders were replaced by the **19 328 real fokontany** from [julkwel/madagascar-map](https://github.com/julkwel/madagascar-map). Every generated address is now fully real down to the fokontany level. Rebuild with `php build-geography.php`.
- **`fakerMg()` helper** (inspired by Laravel's `fake()`): `fakerMg()->address()`, `fakerMg()->phoneNumber()`, etc. Resolves the container singleton inside Laravel; falls back to a standalone instance in plain PHP. `composer.json` now autoloads `src/helpers.php` under `files`.
- **Laravel 13 support** (`illuminate/*` `^13.0`, `orchestra/testbench` `^11.0`, Pest `^3|^4|^5`). CI matrix now covers PHP 8.2/8.3/8.4 × Laravel 10/11/12/13 (Laravel 13 requires PHP ≥ 8.3).
- `MalagasyPhoneNumber` rule now accepts the `+261` country code (e.g. `+261 32 12 345 67`), as documented.

### Fixed
- The package no longer fatals outside a booted Laravel app: config access goes through `Support\Config`, which falls back to defaults when no Laravel `config` repository is available (plain PHP usage of `fakerMg()` was broken before).
- Phone prefixes now come from a single verified source (`PhonePrefixRepository`): 032/037 (Orange), 033/035 (Airtel), 034/038 (Telma/Yas), 039 (bip). The generator and the `MalagasyPhoneNumber` rule can no longer disagree — previously the package generated `035`/`036`/`039` numbers that its own rule rejected.
- `MalagasyCin` rule tests used invalid fixtures that tripped the length check instead of the bureau check.

### Changed
- **`fakerphp/faker` is now optional** (`suggest` instead of `require`): the core generators no longer depend on it. The `fake()->malagasy*()` bridge auto-wires only when FakerPHP is installed.
- `AddressGenerator` and `ContactGenerator` no longer depend on FakerPHP internally; `GeographyRepository` is now an injectable instance instead of a static-only class.
- `fakermg:preview --type=person` now outputs first name, last name and full name.
- Exports (`--export=json|csv`) are written to the system temp directory instead of the current working directory.

### Breaking
- `ContactGenerator` constructor takes no arguments (was `ContactGenerator(Generator $faker)`).
- `GeographyRepository` methods are instance methods (were static); resolve the class from the container.
- Invalid `cin_format` config values now throw `InvalidArgumentException` instead of silently falling back to compact.
