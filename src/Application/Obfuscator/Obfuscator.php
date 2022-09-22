<?php

namespace PhpUniter\PackageLaravel\Application\Obfuscator;

use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;
use PhpUniter\PackageLaravel\Application\Obfuscator\Entity\ObfuscateMap;
use PhpUniter\PackageLaravel\Application\Obfuscator\Exception\ObfuscationFailed;

class Obfuscator
{
    /**
     * @psalm-suppress MixedArgument
     *
     * @throws ObfuscationFailed
     */
    public function obfuscate(ObfuscateMap $map, LocalFile $localFile, callable $getKeySaver): string
    {
        $obfuscated = preg_replace_callback_array(
            $replacements = [
                '/(?<=class\s)(\w+)/'       => $getKeySaver($map::CLASS_NAMES),
                '/(?<=function\s)(\w+)/'    => $getKeySaver($map::METHODS),
                '/(?<=const\s)(\w+)/'       => $getKeySaver($map::CONSTANTS),
                '/(?<=namespace\s)([^;]+)/' => $getKeySaver($map::NAMESPACES),
            ],
            $localFile->getFileBody(),
            -1,
            $count
        );

        if (!is_string($obfuscated)) {
            return '';
        }

        foreach ($map->getMapType($map::METHODS) as $pair) {
            $obfuscated = $this->replaceInText('->', $pair, $obfuscated, '(');
            $obfuscated = $this->replaceInText('::', $pair, $obfuscated, '(');
        }

        foreach ($map->getMapType($map::CONSTANTS) as $pair) {
            $obfuscated = $this->replaceInText('::', $pair, $obfuscated);
        }

        return $obfuscated;
    }

    /**
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedAssignment
     *
     * @throws ObfuscationFailed
     */
    public function deObfuscate(ObfuscateMap $map, string $fileBody): string
    {
        $deObfuscated = $fileBody;

        foreach ($map->getMapType($map::CLASS_NAMES) as $methodPair) {
            $deObfuscated = $this->deReplace($methodPair, $deObfuscated);
        }

        foreach ($map->getMapType($map::METHODS) as $methodPair) {
            $deObfuscated = $this->deReplace($methodPair, $deObfuscated);
        }
        foreach ($map->getMapType($map::CONSTANTS) as $methodPair) {
            $deObfuscated = $this->deReplace($methodPair, $deObfuscated);
        }
        foreach ($map->getMapType($map::NAMESPACES) as $methodPair) {
            $deObfuscated = $this->deReplace($methodPair, $deObfuscated);
        }

        return $deObfuscated;
    }

    private function replaceInText(string $prefix, array $pair, string $subject, string $suffix = ''): string
    {
        $methodInText = $prefix.(string) $pair[1].$suffix;

        return str_replace($methodInText, $prefix.(string) $pair[0].$suffix, $subject);
    }

    /**
     * @param string[] $methodPair
     *
     * @throws ObfuscationFailed
     */
    private function deReplace(array $methodPair, string $deObfuscated): string
    {
        $one = $methodPair[0] ?? null;
        $two = $methodPair[1] ?? null;
        if (is_null($one) || is_null($two)) {
            throw new ObfuscationFailed('Wrong map structure');
        }

        return str_replace($one, $two, $deObfuscated);
    }
}
