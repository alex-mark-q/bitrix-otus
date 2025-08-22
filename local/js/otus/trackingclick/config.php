<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

return [
    'js' => [
        'dist/trackingclick.bundle.js',
    ],
    'rel' => ['main.core'], // Зависимости
    'skip_core' => false,
];
