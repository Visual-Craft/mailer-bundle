<?php

return Symfony\CS\Config\Config::create()
    ->fixers([
        '-phpdoc_to_comment',
        'short_array_syntax',
        'strict_param',
        'strict',
        'phpdoc_order_fixer',
        'php_unit_construct_fixer',
        'php_unit_strict',
        'php4_constructor',
        'concat_with_spaces',
        '-concat_without_spaces',
        '-phpdoc_params',
        '-phpdoc_separation',
        '-no_empty_phpdoc',
    ])
;
