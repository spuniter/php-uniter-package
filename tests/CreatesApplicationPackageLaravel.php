<?php

namespace PhpUniter\PackageLaravel\Tests;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplicationPackageLaravel
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $path = __DIR__.'/../../../../bootstrap/app.php';
        $app = require $path;

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    public static function safeUnlink(string $filePath): bool
    {
        if (file_exists($filePath)) {
            return unlink($filePath);
        }

        return true;
    }
}
