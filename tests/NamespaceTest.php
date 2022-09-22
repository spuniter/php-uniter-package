<?php

namespace PhpUniter\PackageLaravel\Tests;

use Illuminate\Foundation\Testing\TestCase;
use PhpUniter\PackageLaravel\Application\Generation\NamespaceGenerator;

class NamespaceTest extends TestCase
{
    use CreatesApplicationPackageLaravel;

    /**
     * @dataProvider getCases
     *
     * @param string $input
     */
    public function testFindNamespace($input): void
    {
        $namespace = NamespaceGenerator::findNamespace($input);
        self::assertEquals('PhpUniter\PackageLaravel\Tests', $namespace);
    }

    public function getCases(): array
    {
        $fname = __DIR__.'/'.'NamespaceTest.php';

        return [
            [
                file_get_contents($fname),
            ],
        ];
    }
}
