<?php

namespace PhpUniter\PackageLaravel;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use PhpUniter\PackageLaravel\Application\Generation\NamespaceGenerator;
use PhpUniter\PackageLaravel\Application\Generation\PathCorrector;
use PhpUniter\PackageLaravel\Application\Obfuscator\KeyGenerator\RandomMaker;
use PhpUniter\PackageLaravel\Application\Obfuscator\ObfuscatorFabric;
use PhpUniter\PackageLaravel\Application\Obfuscator\Preprocessor;
use PhpUniter\PackageLaravel\Application\PhpUnitService;
use PhpUniter\PackageLaravel\Application\Placer;
use PhpUniter\PackageLaravel\Controller\Console\Cli\GeneratePhpUniterTestCommand;
use PhpUniter\PackageLaravel\Controller\Console\Cli\RegisterPhpUniterUserCommand;
use PhpUniter\PackageLaravel\Infrastructure\Integrations\PhpUniterIntegration;
use PhpUniter\PackageLaravel\Infrastructure\Repository\UnitTestRepository;
use PhpUniter\PackageLaravel\Infrastructure\Repository\UnitTestRepositoryInterface;
use PhpUniter\PackageLaravel\Infrastructure\Request\GenerateClient;
use PhpUniter\PackageLaravel\Infrastructure\Request\GenerateRequest;
use PhpUniter\PackageLaravel\Infrastructure\Request\RegisterRequest;

class PhpUniterPackageLaravelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('php-uniter.php'),
            ], 'config');

            // Registering package commands.
            $this->commands([
                GeneratePhpUniterTestCommand::class,
                RegisterPhpUniterUserCommand::class,
            ]);
        }
    }

    /**
     * Register the application services.
     *
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedOperand
     * @psalm-suppress InvalidScalarArgument
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'php-uniter');

        // Register the main class to use with the facade
        $this->app->singleton('php-uniter', function () {
            return new PhpUniterPackageLaravel();
        });

        $this->app->bind(GenerateClient::class, function (Application $app) {
            return new GenerateClient();
        });

        $this->app->bind(ObfuscatorFabric::class, function (Application $app) {
            return new ObfuscatorFabric();
        });

        $this->app->bind(Placer::class, function (Application $app) {
            return new Placer(
                $app->make(UnitTestRepositoryInterface::class)
            );
        });

        $this->app->bind(Preprocessor::class, function (Application $app) {
            return new Preprocessor(config('php-uniter.preprocess'));
        });

        $this->app->bind(UnitTestRepositoryInterface::class, function (Application $app) {
            return new UnitTestRepository(
                config('php-uniter.projectDirectory')
            );
        });

        $this->app->bind(NamespaceGenerator::class, function (Application $app) {
            return new NamespaceGenerator(config('php-uniter.baseNamespace'), config('php-uniter.unitTestsDirectory'), $app->build(PathCorrector::class));
        });

        $this->app->bind(PhpUnitService::class, function (Application $app) {
            return new PhpUnitService(
                $app->make(PhpUniterIntegration::class),
                $app->make(Placer::class),
                $app->make(RandomMaker::class),
                $app->make(NamespaceGenerator::class),
                config('php-uniter.obfuscate')
            );
        });

        $this->app->bind(GenerateRequest::class, function (Application $app) {
            return new GenerateRequest(
                'POST',
                config('php-uniter.baseUrl').'/api/v1/generator/generate',
                [
                    'accept'        => ['application/json'],
                    'timeout'       => 2,
                ],
                config('php-uniter.accessToken')
            );
        });

        $this->app->bind(RegisterRequest::class, function (Application $app) {
            return new RegisterRequest(
                'POST',

                config('php-uniter.baseUrl').'/api/v1/registration/access-token',

                [
                    'accept'        => ['application/json'],
                    'timeout'       => 2,
                ]
            );
        });
    }
}
