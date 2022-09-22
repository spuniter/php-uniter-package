<?php

namespace PhpUniter\PackageLaravel\Tests;

use Illuminate\Foundation\Testing\TestCase;
use Symfony\Component\Console\Exception\RuntimeException;

class RegisterRemoteTest extends TestCase
{
    use CreatesApplicationPackageLaravel;
    public $container = [];

    public function testCommand()
    {
        $command = $this->artisan('php-uniter:register', [
            'email'          => 'a'.uniqid().'@test.ru',
            'password'       => 'NewMockery',
        ]);

        try {
            $command->assertExitCode(1)
                ->run();
        } catch (RuntimeException $e) {
            echo $e->getMessage();
        }
    }
}
