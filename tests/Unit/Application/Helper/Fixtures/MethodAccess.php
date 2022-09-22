<?php

declare(strict_types=1);

namespace PhpUniter\PackageLaravel\Tests\Unit\Application\Helper\Fixtures;

/**
 * Class MethodAccess.
 */
class MethodAccess
{
    public function publicFunction(string $firstArg): string
    {
        return $firstArg;
    }

    protected function protectedFunction(string $firstArg): string
    {
        return $firstArg;
    }

    private function privateFunction(string $firstArg): string
    {
        return $firstArg;
    }

    public static function publicStaticFunction(string $firstArg): string
    {
        return $firstArg;
    }

    protected static function protectedStaticFunction(string $firstArg): string
    {
        return $firstArg;
    }

    private static function privateStaticFunction(string $firstArg): string
    {
        return $firstArg;
    }
}
