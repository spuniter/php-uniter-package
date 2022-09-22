<?php

namespace PhpUniter\PackageLaravel\Infrastructure\Integrations;

use GuzzleHttp\Exception\GuzzleException;
use PhpUniter\PackageLaravel\Infrastructure\Exception\PhpUnitRegistrationInaccessible;
use PhpUniter\PackageLaravel\Infrastructure\Request\GenerateClient;
use PhpUniter\PackageLaravel\Infrastructure\Request\RegisterRequest;

class PhpUniterRegistration
{
    private GenerateClient $client;
    private RegisterRequest $registerRequest;

    public function __construct(GenerateClient $client, RegisterRequest $registerRequest)
    {
        $this->client = $client;
        $this->registerRequest = $registerRequest;
    }

    /**
     * @throws GuzzleException
     * @throws PhpUnitRegistrationInaccessible
     */
    public function registerPhpUnitUser(string $email, string $password): bool
    {
        $response = $this->client->send(
            $this->registerRequest,
            [
                'json' => [
                    'email'    => $email,
                    'password' => $password,
                ],
            ]
        );

        if (200 !== $response->getStatusCode()) {
            throw new PhpUnitRegistrationInaccessible("Registration failed with error '{$response->getReasonPhrase()}'");
        }

        return true;
    }
}
