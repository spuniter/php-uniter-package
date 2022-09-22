<?php

namespace PhpUniter\PackageLaravel\Application\Generation;

class NamespaceGenerator
{
    private string $testsNamespace;
    private string $testsDirectory;

    private PathCorrector $pathCorrector;

    public function __construct(string $testsNamespace, string $testsDirectory, PathCorrector $pathCorrector)
    {
        $this->testsNamespace = $testsNamespace;
        $this->pathCorrector = $pathCorrector;
        $this->testsDirectory = $testsDirectory;
    }

    public function fetch(string $code): string
    {
        return $this->addNamespace($code, $this->make($code));
    }

    public function makeNamespace(string $srcNamespace): string
    {
        $path = $this->pathCorrector->normaliseBackSlashes($this->testsNamespace.'\\'.$srcNamespace);

        return 'namespace '.$path.';';
    }

    private function make(string $code): string
    {
        $srcNamespace = $this->findNamespace($code);
        $path = $this->pathCorrector->normaliseBackSlashes($this->testsNamespace.'\\'.$srcNamespace);

        return 'namespace '.$path.';';
    }

    public function makePathToTest(string $namespace): string
    {
        return $this->testsDirectory.'/'.$this->pathCorrector->toSlashes($namespace);
    }

    public function addNamespace(string $code, string $namespace): string
    {
        $replace = '<?php'."\n".$namespace."\n";

        return str_replace("<?php\n", $replace, $code);
    }

    public static function findNamespace(string $classText): string
    {
        if (preg_match('/(?<=namespace\s)([^;]+)/', $classText, $matches)) {
            return $matches[0];
        }

        return '';
    }
}
