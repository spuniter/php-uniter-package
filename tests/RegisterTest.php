<?php

namespace PhpUniter\PackageLaravel\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase;
use PhpUniter\PackageLaravel\Application\PhpUnitUserRegisterService;
use PhpUniter\PackageLaravel\Infrastructure\Integrations\PhpUniterRegistration;
use PhpUniter\PackageLaravel\Infrastructure\Request\GenerateClient;
use PhpUniter\PackageLaravel\Infrastructure\Request\RegisterRequest;
use Symfony\Component\Console\Exception\RuntimeException;

class RegisterTest extends TestCase
{
    use CreatesApplicationPackageLaravel;

    public $container = [];

    public function testCommand()
    {
        $this->app->bind(RegisterRequest::class, function (Application $app) {
            return new RegisterRequest('POST', '');
        });

        $this->app->bind(PhpUniterRegistration::class, function (Application $app) {
            $history = Middleware::history($this->container);
            $mock = new MockHandler([
                new Response(200, ['X-Foo' => 'Bar'], json_encode(['token' => 'test'])),
            ]);

            $handlerStack = HandlerStack::create($mock);
            $handlerStack->push($history);
            $client = new GenerateClient(['handler' => $handlerStack]);

            return new PhpUniterRegistration(
                $client,
                $app->make(RegisterRequest::class)
            );
        });

        $this->app->bind(PhpUnitUserRegisterService::class, function (Application $app) {
            return new PhpUnitUserRegisterService($app->make(PhpUniterRegistration::class));
        });

        $command = $this->artisan('php-uniter:register', [
            'email'          => 'a'.uniqid().'@test.ru',
            'password'       => 'NewMockery12',
        ]);

        try {
            $command->assertExitCode(0)
                ->run();
        } catch (RuntimeException $e) {
            echo $e->getMessage();
        }
    }
}
