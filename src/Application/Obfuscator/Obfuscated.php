<?php

namespace PhpUniter\PackageLaravel\Application\Obfuscator;

use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;
use PhpUniter\PackageLaravel\Application\Obfuscator\KeyGenerator\ObfuscateNameMaker;

interface Obfuscated
{
    public function __construct(LocalFile $localFile, ObfuscateNameMaker $keyGenerator, Obfuscator $obfuscator);

    public function getObfuscatedFileBody(): string;

    public function deObfuscate(string $fileBody): string;
}
