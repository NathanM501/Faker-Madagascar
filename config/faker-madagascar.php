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

];
