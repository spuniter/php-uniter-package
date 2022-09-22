<?php

namespace PhpUniter\PackageLaravel\Infrastructure\Repository;

use PhpUniter\PackageLaravel\Application\PhpUniter\Entity\PhpUnitTest;

interface UnitTestRepositoryInterface
{
    public function saveOne(PhpUnitTest $unitTest, string $relativePath, string $className): int;
}
