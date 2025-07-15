<?php

#run >>> vendor/bin/php-cs-fixer fix

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12'                      => true,
        'strict_param'                => true,                                        // fuerza comparaciones estrictas (requiere --allow-risky=yes)
        'array_syntax'                => ['syntax' => 'short'],                       // [] en vez de array()
        'ordered_imports'             => ['sort_algorithm' => 'alpha'],               // ordena los use
        'blank_line_before_statement' => ['statements' => ['return']],                // línea antes de return
        'binary_operator_spaces'      => ['default' => 'align_single_space_minimal'], // alinea =
        'no_extra_blank_lines'        => true,                                        // elimina líneas en blanco extra
        'single_quote'                => true,                                        // usa ' salvo que necesite "
        'trailing_comma_in_multiline' => ['elements' => ['arrays']],                  // coma al final en arrays multilínea
        
        'no_unused_imports'           => true,                                        // elimina imports no usados
        'phpdoc_to_comment'           => true,                                        // convierte phpdoc a comentarios
        'no_superfluous_phpdoc_tags'  => true,                                        // elimina etiquetas phpdoc innecesarias
        'phpdoc_order'                => true,                                        // ordena las etiquetas phpdoc
        'phpdoc_scalar'               => true,                                        // convierte tipos primitivos a phpdoc
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__)
            ->exclude('vendor')
            ->exclude('i18n')
            ->exclude('assets')
    );
