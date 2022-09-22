<?php

declare(strict_types=1);

namespace PhpUniter\PackageLaravel\Tests;

use Illuminate\Foundation\Testing\TestCase;
use PhpUniter\PackageLaravel\PhpUnitTestHelper;
use PhpUniter\PackageLaravel\Tests\Unit\Application\Helper\Fixtures\MethodAccess;

/**
 * Class PhpUnitTestHelperTest.
 */
class PhpUnitTestHelperTest extends TestCase
{
    use CreatesApplicationPackageLaravel;

    private string $pathToTest;
    private string $projectDirectory;

    public function setUp(): void
    {
        parent::setUp();
        $this->pathToTest = (string) config('php-uniter.unitTestsDirectory');
        $this->projectDirectory = base_path();
    }

    /**
     * @covers \PhpUnitTestHelper::makeAllMethodsPublic
     */
    public function testMakeAllMethodsPublic(): void
    {
        $this->app->bind(PhpUnitTestHelper::class, function () {
            return new PhpUnitTestHelper($this->projectDirectory);
        });

        $testHelper = $this->app->make(PhpUnitTestHelper::class);
        $className = $testHelper->makeAllMethodsPublic(MethodAccess::class);

        $this->assertEquals('a', (new $className())->publicFunction('a'));
        $this->assertEquals('a', (new $className())->protectedFunction('a'));
        $this->assertEquals('a', (new $className())->privateFunction('a'));

        $this->assertEquals('a', $className::publicStaticFunction('a'));
        $this->assertEquals('a', $className::protectedStaticFunction('a'));
        $this->assertEquals('a', $className::privateStaticFunction('a'));
    }

    /**
     * @covers \PhpUnitTestHelper::getProxyClassName
     */
    public function testGetProxyClassName(): void
    {
        $this->app->bind(PhpUnitTestHelper::class, function () {
            return new PhpUnitTestHelper($this->projectDirectory);
        });

        $testHelper = $this->app->make(PhpUnitTestHelper::class);
        $className = $testHelper->makeAllMethodsPublic(PhpUnitTestHelper::class);

        $this->assertEquals('a\b\c', $className::getProxyClassName(['a', 'b'], 'c'));
    }

    /**
     * @covers \PhpUnitTestHelper::getClassBody
     */
    public function testGetClassBody(): void
    {
        $this->app->bind(PhpUnitTestHelper::class, function () {
            return new PhpUnitTestHelper($this->projectDirectory);
        });

        $testHelper = $this->app->make(PhpUnitTestHelper::class);
        $className = $testHelper->makeAllMethodsPublic(PhpUnitTestHelper::class);

        $class = new $className($this->projectDirectory);
        $this->assertNotEmpty($class->getClassBody(MethodAccess::class));
    }
}
