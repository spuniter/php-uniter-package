<?php

namespace PhpUniter\PackageLaravel\Application\Obfuscator;

class Preprocessor
{
    private bool $use;

    public function __construct(bool $use)
    {
        $this->use = $use;
    }

    public function preprocess(string $filename, array &$output = null, int &$resultCode = null): int
    {
        if (!$this->use) {
            return 0;
        }

        $cmd = 'php vendor/friendsofphp/php-cs-fixer/php-cs-fixer -q fix --config=.php_cs.dist';
        exec("{$cmd} {$filename}", $output, $resultCode);

        return $resultCode;
    }
}
