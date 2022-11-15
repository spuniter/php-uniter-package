<?php

namespace PhpUniter\PackageLaravel\Infrastructure\Repository;

use PhpUniter\PackageLaravel\Application\File\Exception\DirectoryPathWrong;
use PhpUniter\PackageLaravel\Application\File\Exception\FileNotAccessed;
use PhpUniter\PackageLaravel\Application\Generation\Exception\TestNotCreated;
use PhpUniter\PackageLaravel\Application\PhpUniter\Entity\PhpUnitTest;

class UnitTestRepository implements UnitTestRepositoryInterface
{
    private string $projectRoot;

    public function __construct(string $projectRoot)
    {
        $this->projectRoot = $projectRoot;
    }

    /**
     * @param string $relativePath // path from project root to test to write
     *
     * @throws DirectoryPathWrong
     * @throws FileNotAccessed
     * @throws TestNotCreated
     */
    public function saveOne(PhpUnitTest $unitTest, string $relativePath, string $className): int
    {
        $pathToTest = $this->projectRoot.'/'.$relativePath.'/'.$className;

        $testDir = dirname($pathToTest);
        $touch = $this->touchDir($testDir);

        if (!$touch) {
            throw new DirectoryPathWrong("Directory $testDir cannot be created");
        }

        if (!is_writable($testDir)) {
            throw new DirectoryPathWrong("Directory $testDir is not writable");
        }

        $unitTest->setPathToTest($pathToTest);
        $unitTestText = $unitTest->getFinalUnitTest();

        if (empty($unitTestText)) {
            throw new TestNotCreated('Empty test created');
        }

        if ($size = file_put_contents($pathToTest, $unitTestText)) {
            return $size;
        }

        throw new FileNotAccessed("File $pathToTest was not saved");
    }

    protected function touchDir(string $dirPath): bool
    {
        if (is_dir($dirPath)) {
            return true;
        }

        return mkdir($dirPath, 0777, true);
    }
}
