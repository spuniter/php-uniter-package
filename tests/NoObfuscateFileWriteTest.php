<?php

namespace PhpUniter\PackageLaravel\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase;
use PhpUniter\PackageLaravel\Application\Generation\NamespaceGenerator;
use PhpUniter\PackageLaravel\Application\Obfuscator\KeyGenerator\StableMaker;
use PhpUniter\PackageLaravel\Application\Obfuscator\ObfuscatorFabric;
use PhpUniter\PackageLaravel\Application\PhpUnitService;
use PhpUniter\PackageLaravel\Application\Placer;
use PhpUniter\PackageLaravel\Infrastructure\Integrations\PhpUniterIntegration;
use PhpUniter\PackageLaravel\Infrastructure\Repository\UnitTestRepository;
use PhpUniter\PackageLaravel\Infrastructure\Repository\UnitTestRepositoryInterface;
use PhpUniter\PackageLaravel\Infrastructure\Request\GenerateClient;
use PhpUniter\PackageLaravel\Infrastructure\Request\GenerateRequest;

class NoObfuscateFileWriteTest extends TestCase
{
    use CreatesApplicationPackageLaravel;
    public $container = [];
    /**
     * @var \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private string $pathToTest;
    private string $projectDirectory;

    public function setUp(): void
    {
        parent::setUp();
        $this->pathToTest = (string) config('php-uniter.unitTestsDirectory');
        $this->projectDirectory = base_path();
    }

    /**
     * @dataProvider getInputAndExpected
     */
    public function testCommand($input, $obfExpected, $obfTest, $result)
    {
        $this->app->bind(UnitTestRepositoryInterface::class, UnitTestRepository::class);
        $repository = new UnitTestRepository($this->projectDirectory);
        $this->app->bind(PhpUnitService::class, function (Application $app) use ($repository) {
            return new PhpUnitService($app->make(PhpUniterIntegration::class),
                new Placer($repository),
                new StableMaker(),
                $app->make(NamespaceGenerator::class)
            );
        });
        $this->app->bind(ObfuscatorFabric::class, function (Application $app) {
            return new ObfuscatorFabric();
        });

        $this->app->bind(PhpUniterIntegration::class, function (Application $app) use ($obfTest) {
            $body = json_encode([
                'test'  => $obfTest,
                'code'  => 200,
                'stats' => ['1', '2'],
                'log'   => 'warnings list',
                'class' => 'Foo',
            ]);

            $history = Middleware::history($this->container);
            $mock = new MockHandler([
                new Response(200, ['X-Foo' => 'Bar'], $body),
            ]);

            $handlerStack = HandlerStack::create($mock);
            $handlerStack->push($history);
            $client = new GenerateClient(['handler' => $handlerStack]);

            return new PhpUniterIntegration(
                $client,
                $app->make(GenerateRequest::class)
            );
        });
        chdir(storage_path());
        $delete = self::safeUnlink($this->projectDirectory.'/'.$this->pathToTest.'/Foo/Bar/Application/Barbar/Entity/FooTest.php');

        $command = $this->artisan('php-uniter:generate', [
            'filePath'          => __DIR__.'/Unit/Application/Obfuscator/Entity/Fixtures/SourceClass.php.input',
        ]);
        $command->assertExitCode(0)->expectsOutput('Generated test was written to '.$this->projectDirectory.'/'.$this->pathToTest.'/Foo/Bar/Application/Barbar/Entity/FooTest.php')->execute();

        $requestObfuscatedText = self::getResponseBody($this->container);

        $deObfuscatedTest = file_get_contents($this->projectDirectory.'/'.$this->pathToTest.'/Foo/Bar/Application/Barbar/Entity/FooTest.php');
        $delete = self::safeUnlink($this->projectDirectory.'/'.$this->pathToTest.'/Foo/Bar/Application/Barbar/Entity/FooTest.php');
        self::assertEquals($result, $deObfuscatedTest);

        self::actualize(__DIR__.'/Unit/Application/Obfuscator/Entity/Fixtures/ObfuscatedClass.php.expected', $requestObfuscatedText);
        self::actualize(__DIR__.'/Unit/Application/Obfuscator/Entity/Fixtures/Deobfuscated.test.expected', $deObfuscatedTest);
    }

    public static function getResponseBody(array $container)
    {
        $req = current($container)['request'];
        $contents = $req->getBody()->getContents();
        $re = json_decode($contents);

        return $re->class;
    }

    public function getCases(): array
    {
        return [
            self::getInputAndExpected(),
        ];
    }

    public static function getInputAndExpected(): array
    {
        return [
            [
                file_get_contents(__DIR__.'/Unit/Application/Obfuscator/Entity/Fixtures/SourceClass.php.input'),
                file_get_contents(__DIR__.'/Unit/Application/Obfuscator/Entity/Fixtures/ObfuscatedClass.php.expected'),
                file_get_contents(__DIR__.'/Unit/Application/Obfuscator/Entity/Fixtures/Obfuscated.test.input'),
                file_get_contents(__DIR__.'/Unit/Application/Obfuscator/Entity/Fixtures/Deobfuscated.test.expected'),
            ],
        ];
    }

    public function actualize(string $path, string $actual, $doIt = false): void
    {
        $dirCurrent = getcwd();
        $fileExists = file_exists($this->projectDirectory.'/.actualize');
        if ($doIt || $fileExists) {
            $done = self::updateExpected($path, $actual);
        }
    }

    public static function updateExpected(string $path, string $actual)
    {
        return file_put_contents($path, $actual);
    }

    public static function remSpaces($text)
    {
        return preg_replace('/\s+/', '', $text);
    }
}
