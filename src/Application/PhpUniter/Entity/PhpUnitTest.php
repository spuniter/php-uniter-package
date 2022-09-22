<?php

namespace PhpUniter\PackageLaravel\Application\PhpUniter\Entity;

use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;

class PhpUnitTest
{
    private LocalFile $localFile;
    private string $obfuscatedUnitTest;
    private string $finalUnitTest = '';
    private array $repositories;
    private string $pathToTest = '';

    public function __construct(LocalFile $localFile, string $unitTest, array $repositories = [])
    {
        $this->localFile = $localFile;
        $this->obfuscatedUnitTest = $unitTest;
        $this->repositories = $repositories;
    }

    public function getObfuscatedUnitTest(): string
    {
        return $this->obfuscatedUnitTest;
    }

    public function getLocalFile(): LocalFile
    {
        return $this->localFile;
    }

    public function getFinalUnitTest(): string
    {
        return $this->finalUnitTest;
    }

    public function setFinalUnitTest(string $finalUnitTest): void
    {
        $this->finalUnitTest = $finalUnitTest;
    }

    public function getPathToTest(): string
    {
        return $this->pathToTest;
    }

    public function setPathToTest(string $pathToTest): void
    {
        $this->pathToTest = $pathToTest;
    }

    public function getRepositories(): array
    {
        return $this->repositories;
    }
}
