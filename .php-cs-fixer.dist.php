<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

$header = <<<'HEADER'
    (c) 2021 Michael Joyce <mjoyce@sfu.ca>
    This source file is subject to the GPL v2, bundled
    with this source code in the file LICENSE.
    HEADER;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
    ->in(__DIR__ . '/migrations')
    ->notPath('src/Kernel.php');

$config = new PhpCsFixer\Config();

return $config->setRiskyAllowed(true)
    ->setUsingCache(true)
    ->setCacheFile(__DIR__ . '/var/cache/php_cs.cache')
    ->setFinder($finder)->setRules(
        [
            '@DoctrineAnnotation' => true,

            '@PSR1' => true,
            '@PSR12' => true,

            '@PhpCsFixer' => true,
            '@PhpCsFixer:risky' => true,

            '@PHP74Migration' => true,
            '@PHP74Migration:risky' => true,

            '@Symfony:risky' => true,

            '@PHPUnit84Migration:risky' => true,

            // https://cs.symfony.com/doc/rules/index.html
            // Alias
            'mb_str_functions' => true,
            'no_alias_functions' => [
                'sets' => [
                    '@all',
                ],
            ],

            // Array Notation

            //Basic
            'braces' => [
                'allow_single_line_anonymous_class_with_empty_body' => true,
                'allow_single_line_closure' => true,
                'position_after_functions_and_oop_constructs' => 'same',
                'position_after_control_structures' => 'same',
                'position_after_anonymous_constructs' => 'same',
            ],

            // Casing

            // Class Notation
            'class_attributes_separation' => [
                'elements' => [
                    'const' => 'one',
                    'method' => 'one',
                    'property' => 'one',
                ],
            ],
            'final_class' => false,
            'final_internal_class' => false,
            'no_blank_lines_after_class_opening' => false,
            'ordered_class_elements' => [
                'order' => [
                    'use_trait',
                    'constant',
                    'constant_public',
                    'constant_protected',
                    'constant_private',
                    'public',
                    'protected',
                    'private',
                    'property',
                    'property_static',
                    'property_public',
                    'property_protected',
                    'property_private',
                    'property_public_static',
                    'property_protected_static',
                    'property_private_static',
                    'construct',
                    'magic',
                    'destruct',
                    'method',
                    'method_static',
                    'method_private',
                    'method_public_static',
                    'method_protected_static',
                    'method_private_static',
                    'method_protected',
                    'method_public',
                    'phpunit',
                ],
            ],
            'ordered_interfaces' => true,
            'self_static_accessor' => true,
            'single_class_element_per_statement' => [
                'elements' => ['const', 'property'],
            ],

            // Class usage
            'date_time_immutable' => false,

            // Comment
            'header_comment' => [
                'header' => $header,
            ],
            'single_line_comment_style' => [
                'comment_types' => ['asterisk', 'hash'],
            ],

            // Control Structure
            'no_unneeded_control_parentheses' => [
                'statements' => ['break', 'clone', 'continue', 'echo_print', 'return', 'switch_case', 'yield'],
            ],
            'simplified_if_return' => true,
            'yoda_style' => [
                'always_move_variable' => true,
            ],

            // Function Notation
            'function_declaration' => [
                'closure_function_spacing' => 'none',
            ],
            'method_argument_space' => [
                'on_multiline' => 'ensure_fully_multiline',
            ],
            'native_function_invocation' => false,
            'nullable_type_declaration_for_default_null_value' => true,
            'phpdoc_to_param_type' => true,
            'phpdoc_to_property_type' => true,
            'phpdoc_to_return_type' => true,
            'regular_callable_call' => true,
            'return_type_declaration' => [
                'space_before' => 'one',
            ],
            'static_lambda' => true,

            // Import
            'ordered_imports' => [
                'imports_order' => ['class', 'function', 'const'],
                'sort_algorithm' => 'none',
            ],

            // List Notation
            'list_syntax' => [
                'syntax' => 'long',
            ],

            // Namespace Notation

            // Naming

            // Operator
            'concat_space' => [
                'spacing' => 'one',
            ],
            'increment_style' => [
                'style' => 'post',
            ],
            'not_operator_with_space' => true,
            'operator_linebreak' => [
                'only_booleans' => false,
            ],

            // PHP Tag

            // PHPUnit
            'php_unit_dedicate_assert' => ['target' => 'newest'],
            'php_unit_dedicate_assert_internal_type' => ['target' => 'newest'],
            'php_unit_expectation' => ['target' => 'newest'],
            'php_unit_internal_class' => ['types' => []],
            'php_unit_mock' => ['target' => 'newest'],
            'php_unit_namespaced' => ['target' => 'newest'],
            'php_unit_no_expectation_annotation' => ['target' => 'newest'],
            'php_unit_test_annotation' => ['style' => 'annotation'],
            'php_unit_test_case_static_method_calls' => ['call_type' => 'this'],
            'php_unit_test_class_requires_covers' => false,

            // PHPDoc
            'align_multiline_comment' => [
                'comment_type' => 'all_multiline',
            ],
            'general_phpdoc_annotation_remove' => [
                'annotations' => ['author', 'package', 'subpackage', 'version', 'coversNothing'],
            ],
            'phpdoc_align' => [
                'tags' => ['method', 'param', 'property', 'return', 'throws', 'type', 'var'],
                'align' => 'left',
            ],
            'phpdoc_order_by_value' => [
                'annotations' => ['covers', 'dataProvider', 'depends', 'group', 'method', 'property', 'throws', 'uses'],
            ],
            'phpdoc_tag_casing' => true,
            'phpdoc_types_order' => [
                'sort_algorithm' => 'alpha',
                'null_adjustment' => 'always_first',
            ],

            // Return notation
            'simplified_null_return' => true,

            // Semicolon
            'multiline_whitespace_before_semicolons' => [
                'strategy' => 'no_multi_line',
            ],

            // Strict

            // String notation

            // Whitespace
            'blank_line_before_statement' => [
                'statements' => [
                    'return', 'yield', 'yield_from',
                ],
            ],
        ]
    );
