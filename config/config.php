<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    'accessToken'         => env('PHP_UNITER_ACCESS_TOKEN'),
    'baseUrl'             => env('PHP_UNITER_BASE_URL', 'https://uniter1.tech'),
    'projectDirectory'    => env('PROJECT_DIRECTORY', base_path()),
    'preprocess'          => env('PHP_UNITER_PREPROCESS', true),
    'obfuscate'           => env('PHP_UNITER_OBFUSCATE', true),
    'unitTestBaseClass'   => env('PHP_UNITER_UNIT_TEST_BASE_CLASS', 'PHPUnit\Framework\TestCase'),
    'unitTestsDirectory'  => env('PHP_UNITER_UNIT_TESTS_DIRECTORY', 'storage/tests/Unit'),
    'baseNamespace'       => env('BASE_NAMESPACE', 'Tests\Unit'),
];
