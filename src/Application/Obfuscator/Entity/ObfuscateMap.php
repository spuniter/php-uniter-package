<?php

namespace PhpUniter\PackageLaravel\Application\Obfuscator\Entity;

class ObfuscateMap
{
    public const CLASS_NAMES = 'className';
    public const PROPERTIES = 'properties';
    public const METHODS = 'methods';
    public const CONSTANTS = 'constants';
    public const NAMESPACES = 'namespaces';

    public array $map = [
        self::CLASS_NAMES => [],
        self::PROPERTIES  => [],
        self::METHODS     => [],
        self::CONSTANTS   => [],
        self::NAMESPACES  => [],
    ];

    public function storeKeysAs(string $type, array $matches, string $key): string
    {
        if (is_array($this->map[$type])) {
            $this->map[$type][] = [$key, current($matches)];
        }

        return $key;
    }

    /**
     * @return array|array[]
     */
    public function getMap(): array
    {
        return $this->map;
    }

    public function getMapType(string $type): array
    {
        return array_key_exists($type, $this->map) && is_array($this->map[$type]) ? $this->map[$type] : [];
    }
}
