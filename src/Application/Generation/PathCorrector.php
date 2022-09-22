<?php

namespace PhpUniter\PackageLaravel\Application\Generation;

class PathCorrector
{
    public function toSlashes(string $path): string
    {
        return str_replace('\\', '/', $path);
    }

    public function normaliseBackSlashes(string $path): string
    {
        $path = str_replace('/', '\\', $path);

        return str_replace('\\\\', '\\', $path);
    }

    public function subtract(string $string, string $prefix): string
    {
        return substr($string, strlen($prefix));
    }
}
