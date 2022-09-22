<?php

namespace PhpUniter\PackageLaravel\Application;

use PhpUniter\PackageLaravel\Infrastructure\Exception\PhpUnitRegistrationInaccessible;
use PhpUniter\PackageLaravel\Infrastructure\Integrations\PhpUniterRegistration;

class PhpUnitUserRegisterService
{
    private PhpUniterRegistration $registration;

    public function __construct(
        PhpUniterRegistration $registration
    ) {
        $this->registration = $registration;
    }

    /**
     * @throws PhpUnitRegistrationInaccessible
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \PhpUniter\PackageLaravel\Infrastructure\Exception\PhpUnitTestInaccessible
     */
    public function process(string $email, string $password): bool
    {
        return $this->registration->registerPhpUnitUser($email, $password);
    }
}
