<?php

declare(strict_types=1);

/*
 * Standalone smoke test: uses ONLY vendor/autoload.php — no Laravel, no Testbench.
 * This is exactly what a plain-PHP user of the package would experience,
 * and it exercises the helper's standalone fallback path.
 */

require __DIR__ . '/vendor/autoload.php';

use Manguithre\FakerMadagascar\Data\GeographyRepository;
use Manguithre\FakerMadagascar\Data\PhonePrefixRepository;
use Manguithre\FakerMadagascar\FakerMadagascar;
use Manguithre\FakerMadagascar\Rules\MalagasyCin;
use Manguithre\FakerMadagascar\Rules\MalagasyPhoneNumber;

$ok = 0;
$fail = 0;
$failures = [];

function check(bool $cond, string $label, int &$ok, int &$fail, array &$failures): void
{
    if ($cond) {
        ++$ok;
        echo "  PASS  $label\n";
    } else {
        ++$fail;
        $failures[] = $label;
        echo "  FAIL  $label\n";
    }
}

function ruleErrors(object $rule, mixed $value): array
{
    $errors = [];
    $rule->validate('field', $value, function (string $m) use (&$errors): void {
        $errors[] = $m;
    });

    return $errors;
}

echo "=== 1. Helper (standalone, no Laravel) ===\n";
try {
    $faker = fakerMg();
    check($faker instanceof FakerMadagascar, 'fakerMg() returns a FakerMadagascar instance', $ok, $fail, $failures);
} catch (\Throwable $e) {
    check(false, 'fakerMg() usable standalone: ' . $e->getMessage(), $ok, $fail, $failures);
    $faker = null;
}

echo "\n=== 2. Address generation (real data consistency) ===\n";
if ($faker instanceof FakerMadagascar) {
    $address = $faker->address();
    check($address->region !== '' && $address->district !== '' && $address->commune !== '' && $address->fokontany !== '', 'address() fills region/district/commune/fokontany', $ok, $fail, $failures);

    $regions = (new GeographyRepository())->regions();
    check(in_array($address->region, $regions, true), "generated region '{$address->region}' exists in geography data", $ok, $fail, $failures);

    // Parent/child consistency: the district must really belong to the region
    $districts = (new GeographyRepository())->districtsIn($address->region);
    check(in_array($address->district, $districts, true), "district '{$address->district}' belongs to region '{$address->region}'", $ok, $fail, $failures);

    $forced = $faker->address(region: 'analamanga');
    check($forced->region === 'ANALAMANGA', 'case-insensitive forced region resolves to ANALAMANGA', $ok, $fail, $failures);

    try {
        $faker->address(region: 'ZZZ-UNKNOWN');
        check(false, 'unknown forced region throws', $ok, $fail, $failures);
    } catch (\InvalidArgumentException) {
        check(true, 'unknown forced region throws', $ok, $fail, $failures);
    }

    echo '  Exemple: ' . (string) $address . "\n";
}

echo "\n=== 3. Person ===\n";
if ($faker instanceof FakerMadagascar) {
    $names = [];
    for ($i = 0; $i < 30; ++$i) {
        $names[] = $faker->fullName();
    }
    check(count(array_filter($names, fn ($n) => $n !== '' && str_contains($n, ' '))) === 30, '30/30 full names are well formed', $ok, $fail, $failures);
    check(count(array_unique($names)) > 1, 'names have variety (' . count(array_unique($names)) . '/30 unique)', $ok, $fail, $failures);
    echo '  Exemple: ' . $names[0] . "\n";
}

echo "\n=== 4. Contact ===\n";
if ($faker instanceof FakerMadagascar) {
    $phones = [];
    for ($i = 0; $i < 200; ++$i) {
        $phones[] = $faker->phoneNumber();
    }
    check(count(array_filter($phones, fn ($p) => preg_match('/^0(32|33|34|35|37|38|39)\d{7}$/', $p) === 1)) === 200, '200/200 phones match the verified prefix list', $ok, $fail, $failures);
    check(PhonePrefixRepository::isValidPrefix($faker->phoneNumber('airtel')), 'operator filter (airtel) works', $ok, $fail, $failures);
    check(ruleErrors(new MalagasyPhoneNumber(), $phones[0]) === [], 'generated phone passes its own validation rule', $ok, $fail, $failures);
    check(count(ruleErrors(new MalagasyCin(), $faker->cin())) === 0, 'generated CIN passes its own validation rule', $ok, $fail, $failures);
    check(preg_match('/^\\d{12}$/', $cinExample = $faker->cin()) === 1, 'CIN is exactly 12 digits, no separators', $ok, $fail, $failures);
    check(ruleErrors(new MalagasyCin(), '1234-5678-9012') === [], 'rule tolerates separators typed by users', $ok, $fail, $failures);
    check(ruleErrors(new MalagasyCin(), '12345678') !== [], 'rule rejects the old 8-digit format', $ok, $fail, $failures);
    check(ruleErrors(new MalagasyPhoneNumber(), '+261 32 12 345 67') === [], 'rule accepts +261 international form', $ok, $fail, $failures);
    check(ruleErrors(new MalagasyPhoneNumber(), '0311234567') !== [], 'rule rejects invalid prefix 031', $ok, $fail, $failures);
    echo '  Exemple: ' . $phones[0] . ' / ' . $cinExample . "\n";
}

echo "\n=== 5. Securite ===\n";
$src = '';
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/src')) as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $src .= file_get_contents($file->getRealPath());
    }
}
check(!preg_match('/\brand\(|mt_rand\(|uniqid\(/', $src), 'no weak randomness (rand/mt_rand/uniqid) anywhere in src/', $ok, $fail, $failures);
check(!preg_match('/eval\(|exec\(|shell_exec\(|system\(/', $src), 'no eval/exec/system in src/', $ok, $fail, $failures);
check(json_validate((string) file_get_contents(__DIR__ . '/resources/data/geography.json')), 'geography.json is valid JSON', $ok, $fail, $failures);

echo "\n=== 6. Poids (memoire) ===\n";
$before = memory_get_usage(true);
$repo = new GeographyRepository();
$repo->regions();
$after = memory_get_usage(true);
echo '  Geography JSON charge en memoire : ' . round(($after - $before) / 1024 / 1024, 2) . " Mo\n";
$before = memory_get_usage(true);
$faker->address();
$after = memory_get_usage(true);
echo '  Une generation d\'adresse apres warm-up : ' . max(0, $after - $before) . " octets supplementaires\n";

echo "\n================================\n";
echo "RESULTAT: $ok OK / $fail FAIL\n";
if ($failures !== []) {
    echo "Echecs:\n" . implode("\n", array_map(fn ($f) => " - $f", $failures)) . "\n";
    exit(1);
}
