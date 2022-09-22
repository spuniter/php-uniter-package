<?php

namespace PhpUniter\PackageLaravel\Application\Obfuscator;

use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;
use PhpUniter\PackageLaravel\Application\File\Exception\CodeTypeWrong;
use PhpUniter\PackageLaravel\Application\Obfuscator\Entity\ObfuscatedClass;
use PhpUniter\PackageLaravel\Application\Obfuscator\KeyGenerator\ObfuscateNameMaker;

class ObfuscatorFabric
{
    public const TYPE_CLASS = 'class';
    public const TYPE_PROCEDURAL = 'procedural';

    public function getObfuscated(LocalFile $obfuscatable, ObfuscateNameMaker $keyGenerator): ?Obfuscated
    {
        if ($this->isObfuscatable($obfuscatable)) {
            return new ObfuscatedClass(
                $obfuscatable,
                $keyGenerator,
                new Obfuscator(),
            );
        }

        return null;
    }

    /**
     * @throws CodeTypeWrong
     */
    public function createFile(string $filePath): LocalFile
    {
        $fileBody = file_get_contents($filePath);

        switch ($this->getFileType($fileBody)) {
            case self::TYPE_CLASS:
                return new LocalFile($filePath, $fileBody);
            default:
                throw new CodeTypeWrong('File '.$filePath.' can not be obfuscated: code type is not supported');
        }
    }

    public function isObfuscatable(LocalFile $obfuscatable): bool
    {
        $filePath = $obfuscatable->getFilePath();
        $fileBody = file_get_contents($filePath);

        return self::TYPE_CLASS == $this->getFileType($fileBody);
    }

    private function getFileType(string $fileBody): ?string
    {
        $isClassFile = preg_match('/(?<=class\s)(\w+)/', $fileBody);

        if ($isClassFile) {
            return self::TYPE_CLASS;
        }

        return null;
    }
}
