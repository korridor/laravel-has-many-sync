<?php

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        '@PSR12:risky' => true,
        'array_syntax' => ['syntax' => 'short'],
        'concat_space' => ['spacing' => 'one'],
        'declare_strict_types' => true,
        'modernize_types_casting' => true,
        'void_return' => true,
    ])
    ->setUsingCache(true)
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__.'/src')
            ->in(__DIR__.'/tests')
    )
    ;
