<?php

declare(strict_types=1);

/*
 * PHP-CS-Fixer configuration for manguithre/faker-madagascar.
 *
 * Curated ruleset inspired by FakerPHP/Faker's own .php-cs-fixer.rules.php,
 * reduced to the rules that matter for this package:
 *   - PSR-12 compliance and consistent arrays/operators/quotes,
 *   - strict_types enforced everywhere,
 *   - a guard that keeps randomness cryptographic (random_int, never rand()).
 *
 * Behavior-changing rules beyond that (strict_comparison, static_lambda, ...)
 * are deliberately excluded: style tools must never silently change logic.
 */

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude(['vendor', 'node_modules', '_source', '.build-data', '.freebuff'])
;

return (new PhpCsFixer\Config('faker_madagascar'))
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,

        // Arrays
        'array_indentation' => true,
        'array_syntax' => ['syntax' => 'short'],
        'trailing_comma_in_multiline' => ['elements' => ['arguments', 'arrays']],
        'trim_array_spaces' => true,
        'whitespace_after_comma_in_array' => true,
        'no_whitespace_before_comma_in_array' => true,
        'no_trailing_comma_in_singleline' => ['elements' => ['array']],

        // Operators, casts, spaces
        'concat_space' => ['spacing' => 'one'],
        'binary_operator_spaces' => true,
        'unary_operator_spaces' => true,
        'ternary_operator_spaces' => true,
        'ternary_to_null_coalescing' => true,
        'cast_spaces' => true,
        'lowercase_cast' => true,
        'short_scalar_cast' => true,
        'modernize_types_casting' => true,
        'increment_style' => true,
        'return_type_declaration' => true,
        'type_declaration_spaces' => ['elements' => ['function']],

        // Strictness & safety
        'declare_equal_normalize' => true,
        'declare_parentheses' => true,
        'declare_strict_types' => true,
        'random_api_migration' => true,
        'no_alias_functions' => true,
        'is_null' => true,

        // Control flow
        'no_empty_statement' => true,
        'no_useless_else' => true,
        'no_superfluous_elseif' => true,
        'no_unneeded_braces' => true,
        'no_unneeded_control_parentheses' => true,

        // Namespaces, imports, visibility
        'no_leading_import_slash' => true,
        'no_leading_namespace_whitespace' => true,
        'ordered_imports' => true,
        'no_unused_imports' => true,
        'global_namespace_import' => [
            'import_classes' => false,
            'import_constants' => false,
            'import_functions' => false,
        ],
        'visibility_required' => ['elements' => ['const', 'method', 'property']],
        'class_attributes_separation' => ['elements' => ['method' => 'one']],

        // Whitespace hygiene
        'blank_line_after_opening_tag' => true,
        'no_extra_blank_lines' => true,
        'no_whitespace_in_blank_line' => true,
        'single_quote' => true,
        'single_space_around_construct' => true,
        'single_trait_insert_per_statement' => true,

        // Docblocks
        'no_empty_phpdoc' => true,
        'phpdoc_indent' => true,
        'phpdoc_no_alias_tag' => ['replacements' => ['link' => 'see', 'type' => 'var']],
        'phpdoc_no_package' => true,
        'phpdoc_scalar' => true,
        'phpdoc_separation' => true,
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_trim' => true,
        'phpdoc_types' => true,
        'phpdoc_types_order' => ['null_adjustment' => 'always_last', 'sort_algorithm' => 'alpha'],
        'phpdoc_var_without_name' => true,
    ])
    ->setCacheFile(__DIR__.'/.php-cs-fixer.cache')
    ->setFinder($finder)
;
