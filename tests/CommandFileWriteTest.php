<?php

namespace PhpUniter\PackageLaravel\Tests;

use Illuminate\Foundation\Testing\TestCase;

class CommandFileWriteTest extends TestCase
{
    use CreatesApplicationPackageLaravel;

    public array $container = [];
    private string $pathToTest;
    private string $projectRoot;

    public function setUp(): void
    {
        parent::setUp();
        $this->pathToTest = (string) config('php-uniter.unitTestsDirectory');
        $this->projectRoot = base_path();
    }

    public function testIsWritable()
    {
        self::assertIsWritable($this->projectRoot.'/'.$this->pathToTest);
    }
}
