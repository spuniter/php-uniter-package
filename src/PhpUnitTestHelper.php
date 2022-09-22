<?php

declare(strict_types=1);

namespace PhpUniter\PackageLaravel;

use Composer\Autoload\ClassLoader;
use PhpUniter\PackageLaravel\Infrastructure\Exception\ClassNotFound;

/**
 * Class PhpUnitTestHelper.
 * useful to make All Methods Public.
 */
class PhpUnitTestHelper
{
    /**
     * @param string $fullyQualifiedClassName Fully qualified class name with namespace
     *
     * @return string|null Fully qualified proxy class name with namespace or null
     */
    public static function makeAllMethodsPublic(string $fullyQualifiedClassName): ?string
    {
        $classNameExploded = explode('\\', $fullyQualifiedClassName);
        $className = array_pop($classNameExploded);

        $proxyClassName = "${className}".uniqid();

        try {
            $proxyClassBody = self::renderProxyClass($fullyQualifiedClassName, $className, $proxyClassName);

            self::loadClass($proxyClassName, $proxyClassBody);

            $fullyQualifiedProxyClassName = self::getProxyClassName($classNameExploded, $proxyClassName);
        } catch (ClassNotFound $exception) {
            return null;
        }

        return $fullyQualifiedProxyClassName;
    }

    /**
     * @throws ClassNotFound
     * @psalm-suppress UnresolvableInclude
     */
    private static function getClassBody(string $fullyQualifiedClassName): string
    {
        $path = realpath(self::loadPath());
        if ($path) {
            /** @var ClassLoader $loader */
            $loader = require $path;

            if ($classFilePath = $loader->findFile($fullyQualifiedClassName)) {
                if ($classBody = file_get_contents($classFilePath)) {
                    return $classBody;
                }
            }
        }

        throw new ClassNotFound("Class {$fullyQualifiedClassName} not found or not available by path $classFilePath");
    }

    /**
     * @psalm-suppress UnresolvableInclude
     */
    private static function loadClass(string $proxyFileName, string $proxyClassBody): void
    {
        $fileName = __DIR__."/${proxyFileName}.php";

        file_put_contents($fileName, $proxyClassBody);

        include $fileName;

        unlink($fileName);
    }

    /**
     * @param string[] $classNameExploded
     */
    private static function getProxyClassName(array $classNameExploded, string $proxyClassName): string
    {
        $classNameExploded[] = $proxyClassName;

        return implode('\\', $classNameExploded);
    }

    /**
     * @throws ClassNotFound
     */
    private static function renderProxyClass(string $fullyQualifiedClassName, string $className, string $proxyClassName): string
    {
        $classBody = self::getClassBody($fullyQualifiedClassName);

        return preg_replace(
            ["/class\s+${className}/i", '/(|public|private|protected)\s+(static\s+)?function/i'],
            ["class $proxyClassName", 'public $2function'],
            $classBody
        );
    }

    private static function loadPath(): string
    {
        return (string) env('PROJECT_DIRECTORY') ? (string) env('PROJECT_DIRECTORY').'/vendor/autoload.php' : (__DIR__.'/../../../autoload.php');
    }
}
