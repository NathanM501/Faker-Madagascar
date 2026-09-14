<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Active Regions
    |--------------------------------------------------------------------------
    |
    | Restrict generated data to specific regions. Leave empty for all regions.
    | Region names are case-insensitive (e.g. 'Analamanga' or 'ANALAMANGA').
    |
    | When set, forcing a region outside this list (e.g. FakerMg::address(region: 'X'))
    | throws Manguithre\FakerMadagascar\Exceptions\InvalidArgumentException.
    |
    */

    'active_regions' => [],

    /*
    |--------------------------------------------------------------------------
    | CIN Format
    |--------------------------------------------------------------------------
    |
    | Format of generated CIN numbers: 'compact' (8 digits, e.g. 01010001),
    | 'separated' (e.g. 01-01-0001) or 'auto' (random pick per call).
    |
    */

    'cin_format' => 'auto',

];
