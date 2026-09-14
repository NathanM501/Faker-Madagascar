<?php

declare(strict_types=1);

/*
 * Reconstruit resources/data/geography.json avec les vrais fokontany.
 *
 * Source des données : https://github.com/julkwel/madagascar-map (MIT)
 * Fichier : liste_fokontany_par_commune_data.json
 * Structure source : { Region: { Commune: [ {commune, region, fokontany, district}, ... ] } }
 *
 * La hiérarchie régions → districts → communes provient du geography.json
 * actuel ; seuls les fokontany (placeholders) sont remplacés par les vrais.
 *
 * Usage : php build-geography.php
 */

$geoPath = __DIR__ . '/resources/data/geography.json';
$srcPath = __DIR__ . '/_source/fokontany.json';

$geo = json_decode((string) file_get_contents($geoPath), true, 512, JSON_THROW_ON_ERROR);
$src = json_decode((string) file_get_contents($srcPath), true, 512, JSON_THROW_ON_ERROR);

$normalize = static fn (string $s): string => mb_strtolower(preg_replace('/\s+/u', ' ', trim($s)) ?? trim($s));

// Index source : "region|commune" (normalisés) -> fokontany distincts.
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
                continue; // entête d'exemple du fichier
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

    // NB : pas de "?? []" ici — il créerait une copie temporaire et les
    // écritures par référence se perdraient (bug constaté).
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
    fwrite(STDERR, "ATTENTION : $communesMisses communes sans fokontany dans la source.\n");
    fwrite(STDERR, 'Exemples : ' . implode(' | ', $missingSample) . "\n");
}

$json = json_encode($geo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
file_put_contents($geoPath, $json);

echo "Communes enrichies : $replaced / $communesTotal\n";
echo 'Fokontany distincts au total : ' . count($byRegionCommune ? array_merge(...array_values(array_map('array_keys', $byRegionCommune))) : []) . "\n";
echo 'Taille du fichier final : ' . round(strlen((string) $json) / 1024 / 1024, 2) . " Mo\n";
