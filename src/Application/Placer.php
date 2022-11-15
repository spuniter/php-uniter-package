<?php

namespace PhpUniter\PackageLaravel\Application;

use PhpUniter\PackageLaravel\Application\File\Exception\DirectoryPathWrong;
use PhpUniter\PackageLaravel\Application\File\Exception\FileNotAccessed;
use PhpUniter\PackageLaravel\Application\Generation\Exception\TestNotCreated;
use PhpUniter\PackageLaravel\Application\PhpUniter\Entity\PhpUnitTest;
use PhpUniter\PackageLaravel\Infrastructure\Repository\UnitTestRepositoryInterface;

class Placer
{
    private UnitTestRepositoryInterface $repository;

    public function __construct(UnitTestRepositoryInterface $fileRepository)
    {
        $this->repository = $fileRepository;
    }

    /**
     * @throws DirectoryPathWrong
     * @throws FileNotAccessed
     * @throws TestNotCreated
     *
     * @param string $relativePath // path from project root to test to write
     */
    public function placeUnitTest(PhpUnitTest $phpUnitTest, string $relativePath, string $className): int
    {
        return $this->repository->saveOne($phpUnitTest, $relativePath, $className);
    }
}
