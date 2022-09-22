<?php

namespace PhpUniter\PackageLaravel\Tests\Unit\Application\Obfuscator\Entity;

use PHPUnit\Framework\TestCase;
use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;
use PhpUniter\PackageLaravel\Application\Obfuscator\Entity\ObfuscatedClass;
use PhpUniter\PackageLaravel\Application\Obfuscator\KeyGenerator\StableMaker;
use PhpUniter\PackageLaravel\Application\Obfuscator\Obfuscator;

class ObfuscatedClassTest extends TestCase
{
    /**
     * @dataProvider getObfuscatedFileBody
     */
    public function testGetObfuscated($input, $expected)
    {
        $localFile = new LocalFile('', $input);
        $obfuscatedClassObject = new ObfuscatedClass(
            $localFile,
            new StableMaker(),
            new Obfuscator()
        );
        $obfuscated = $obfuscatedClassObject->getObfuscatedFileBody();
        $this->assertEquals(trim($expected), trim($obfuscated));

        $deObfuscated = $obfuscatedClassObject->deObfuscate($obfuscated);
        $this->assertEquals($input, $deObfuscated);
    }

    public function getObfuscatedFileBody()
    {
        return [
            [
                file_get_contents(__DIR__.'/Fixtures/SourceClass.php.input'),
                file_get_contents(__DIR__.'/Fixtures/ObfuscatedClass.php.expected'),
            ],
        ];
    }
}
