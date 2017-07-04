<?php

return PhpCsFixer\Config::create()
        ->setRiskyAllowed(true)
        ->setRules([
            '@PHP56Migration'         => true,
            '@PSR2'                   => true,
            'array_syntax'            => ['syntax' => 'short'],
            'binary_operator_spaces' => [
                'align_double_arrow' => true,
                'align_equals'       => true
            ],
            'single_quote'           => true,
        ])
        ->setFinder(
            PhpCsFixer\Finder::create()
            ->exclude('tests/Fixtures')
            ->in(__DIR__)
        )
;
