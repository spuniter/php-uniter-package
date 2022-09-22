<?php

namespace PhpUniter\PackageLaravel\Tests;

use Illuminate\Foundation\Testing\TestCase;
use Symfony\Component\Console\Exception\RuntimeException;

class EmptyFileMockTest extends TestCase
{
    use CreatesApplicationPackageLaravel;

    public function testCommand(): void
    {
        $command = $this->artisan('php-uniter:generate', []);
        $this->expectException(RuntimeException::class);
        $command->run();
    }
}
