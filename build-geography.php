<?php

declare(strict_types=1);

/**
 * Rebuilds resources/data/geography.json with real fokontany data.
 *
 * This is a maintenance script for package maintainers only.
 * It is not intended for end users of the package.
 *
 * Data source: https://github.com/julkwel/madagascar-map (MIT License)
 * Source file: _source/fokontany.json
 * Source structure: { Region: { Commune: [ {commune, region, fokontany, district}, ... ] } }
 *
 * The region → district → commune hierarchy comes from the existing geography.json;
 * only the fokontany placeholders are replaced with real names.
 *
 * Usage: php build-geography.php
 */

$geoPath = __DIR__ . '/resources/data/geography.json';
$srcPath = __DIR__ . '/_source/fokontany.json';

$geo = json_decode((string) file_get_contents($geoPath), true, 512, JSON_THROW_ON_ERROR);
$src = json_decode((string) file_get_contents($srcPath), true, 512, JSON_THROW_ON_ERROR);

$normalize = static fn(string $s): string => mb_strtolower(preg_replace('/\s+/u', ' ', trim($s)) ?? trim($s));

$byRegionCommune = [];
$byCommuneOnly = [];

foreach ($src as $region => $communes) {
    if (!is_array($communes)) {
        continue;
    }

    foreach ($communes as $entries) {
        if (!is_array($entries)) {
            continue;
        }

        foreach ($entries as $e) {
            if (!is_array($e)) {
                continue;
            }

            $fk = trim((string) ($e['fokontany'] ?? ''));
            $r = trim((string) ($e['region'] ?? $region));
            $c = trim((string) ($e['commune'] ?? ''));

            if ($fk === '' || $fk === 'Fokontany' || $r === '' || $c === '') {
                continue;
            }

            $nr = $normalize($r);
            $nc = $normalize($c);

            $byRegionCommune[$nr . '|' . $nc][$fk] = true;
            $byCommuneOnly[$nc][$fk] = true;
        }
    }
}

$replaced = 0;
$communesTotal = 0;
$communesMisses = 0;
$missingSample = [];

foreach ($geo['regions'] as $regionName => &$regionNode) {
    $nr = $normalize((string) $regionName);

    if (!isset($regionNode['districts']) || !is_array($regionNode['districts'])) {
        continue;
    }

    foreach ($regionNode['districts'] as &$districtNode) {
        if (!isset($districtNode['communes']) || !is_array($districtNode['communes'])) {
            continue;
        }

        foreach ($districtNode['communes'] as $communeName => &$communeNode) {
            ++$communesTotal;

            $nc = $normalize((string) $communeName);

            $candidates = $byRegionCommune[$nr . '|' . $nc]
                ?? $byCommuneOnly[$nc]
                ?? [];

            if ($candidates !== []) {
                $communeNode['fokontany'] = array_keys($candidates);
                ++$replaced;
            } else {
                ++$communesMisses;

                if (count($missingSample) < 8) {
                    $missingSample[] = "$regionName / $communeName";
                }
            }
        }

        unset($communeNode);
    }

    unset($districtNode);
}

unset($regionNode);

if ($communesMisses > 0) {
    fwrite(STDERR, "WARNING: $communesMisses communes without fokontany in source.\n");
    fwrite(STDERR, 'Examples: ' . implode(' | ', $missingSample) . "\n");
}

$json = json_encode($geo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
file_put_contents($geoPath, $json);

echo "Communes enriched: $replaced / $communesTotal\n";
echo 'Total distinct fokontany: ' . count($byRegionCommune ? array_merge(...array_values(array_map('array_keys', $byRegionCommune))) : []) . "\n";
echo 'Final file size: ' . round(strlen((string) $json) / 1024 / 1024, 2) . " MiB\n";