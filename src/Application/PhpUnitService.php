<?php

namespace PhpUniter\PackageLaravel\Application;

use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;
use PhpUniter\PackageLaravel\Application\File\Exception\ObfucsatorNull;
use PhpUniter\PackageLaravel\Application\Generation\NamespaceGenerator;
use PhpUniter\PackageLaravel\Application\Obfuscator\Entity\ObfuscatedClass;
use PhpUniter\PackageLaravel\Application\Obfuscator\KeyGenerator\ObfuscateNameMaker;
use PhpUniter\PackageLaravel\Application\Obfuscator\ObfuscatorFabric;
use PhpUniter\PackageLaravel\Application\PhpUniter\Entity\PhpUnitTest;
use PhpUniter\PackageLaravel\Application\PhpUniter\Exception\GeneratedTestEmpty;
use PhpUniter\PackageLaravel\Application\PhpUniter\Exception\LocalFileEmpty;
use PhpUniter\PackageLaravel\Infrastructure\Integrations\PhpUniterIntegration;

class PhpUnitService
{
    private Placer $testPlacer;
    private PhpUniterIntegration $integration;
    private ObfuscateNameMaker $keyGenerator;
    private bool $toObfuscate;
    private NamespaceGenerator $namespaceGenerator;

    public function __construct(
        PhpUniterIntegration $phpUniterIntegration,
        Placer $testPlacer,
        ObfuscateNameMaker $keyGenerator,
        NamespaceGenerator $namespaceGenerator,
        bool $toObfuscate = true
    ) {
        $this->integration = $phpUniterIntegration;
        $this->testPlacer = $testPlacer;
        $this->keyGenerator = $keyGenerator;
        $this->toObfuscate = $toObfuscate;
        $this->namespaceGenerator = $namespaceGenerator;
    }

    /**
     * @throws File\Exception\DirectoryPathWrong
     * @throws File\Exception\FileNotAccessed
     * @throws \PhpUniter\PackageLaravel\Application\Obfuscator\Exception\ObfuscationFailed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \PhpUniter\PackageLaravel\Infrastructure\Exception\PhpUnitTestInaccessible
     * @throws GeneratedTestEmpty
     * @throws ObfucsatorNull
     * @throws LocalFileEmpty
     */
    public function process(LocalFile $classFile, ObfuscatorFabric $obfuscatorFabric): PhpUnitTest
    {
        $obfuscated = $classFile;

        if ($this->toObfuscate) {
            $obfuscator = $obfuscatorFabric->getObfuscated($obfuscated, $this->keyGenerator);

            if (is_null($obfuscator)) {
                throw new ObfucsatorNull('File is not obfuscatable');
            }

            /** @var LocalFile $obfuscatedSourceFile */
            /** @var ObfuscatedClass $obfuscator */
            $obfuscatedSourceFile = $obfuscator->makeObfuscated();
            $phpUnitTest = $this->integration->generatePhpUnitTest($obfuscatedSourceFile);
            $testObfuscatedGenerated = $phpUnitTest->getObfuscatedUnitTest();

            $deObfuscated = $obfuscator->deObfuscate($testObfuscatedGenerated);
            $phpUnitTest->setFinalUnitTest($deObfuscated);
        } else {
            $phpUnitTest = $this->integration->generatePhpUnitTest($classFile);
            $phpUnitTest->setFinalUnitTest($phpUnitTest->getObfuscatedUnitTest());
        }

        $classText = $classFile->getFileBody();
        $className = $this->findClassName($classFile);

        $srcNamespace = $this->namespaceGenerator->findNamespace($classText);
        $testNamespace = $this->namespaceGenerator->makeNamespace($srcNamespace);
        $testCode = $this->namespaceGenerator->addNamespace($phpUnitTest->getFinalUnitTest(), $testNamespace);
        $relativePath = $this->namespaceGenerator->makePathToTest($srcNamespace);

        $phpUnitTest->setFinalUnitTest($testCode);

        $testSize = $this->testPlacer->placeUnitTest($phpUnitTest, $relativePath, $className.'Test.php');

        if (empty($testSize)) {
            throw new GeneratedTestEmpty('Empty test written');
        }

        return $phpUnitTest;
    }

    public function findClassName(LocalFile $classFile): string
    {
        $text = $classFile->getFileBody();
        preg_match('/(?<=class\s)(\w+)/', $text, $matches);

        return $matches[0];
    }
}
