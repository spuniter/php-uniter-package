<?php

namespace PhpUniter\PackageLaravel;

use Illuminate\Support\Facades\Facade;

class PhpUniterPackageLaravelFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'php-uniter';
    }
}
