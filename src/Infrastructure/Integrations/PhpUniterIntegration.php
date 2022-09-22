<?php

namespace PhpUniter\PackageLaravel\Infrastructure\Integrations;

use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;
use PhpUniter\PackageLaravel\Application\PhpUniter\Entity\PhpUnitTest;
use PhpUniter\PackageLaravel\Infrastructure\Exception\PhpUnitRegistrationInaccessible;
use PhpUniter\PackageLaravel\Infrastructure\Exception\PhpUnitTestInaccessible;
use PhpUniter\PackageLaravel\Infrastructure\Request\GenerateClient;
use PhpUniter\PackageLaravel\Infrastructure\Request\GenerateRequest;

class PhpUniterIntegration
{
    private GenerateClient $client;
    private GenerateRequest $generateRequest;

    public function __construct(GenerateClient $client, GenerateRequest $generateRequest)
    {
        $this->client = $client;
        $this->generateRequest = $generateRequest;
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws PhpUnitRegistrationInaccessible
     */
    public function generatePhpUnitTest(LocalFile $localFile): PhpUnitTest
    {
        $response = $this->client->send(
            $this->generateRequest,
            [
                'json' => [
                    'class'          => $localFile->getFileBody(),
                    'access_token'   => $this->generateRequest->getToken(),
                ],
            ]
        );

        if (200 !== $response->getStatusCode()) {
            throw new PhpUnitTestInaccessible("Generation failed with error '{$response->getReasonPhrase()}'");
        }

        $generatedTestJson = $response->getBody()->getContents();
        /** @var string[] $generatedTest */
        $generatedTest = json_decode($generatedTestJson, true);
        $generatedTestText = $generatedTest['test'];

        return new PhpUnitTest(
            $localFile,
            $generatedTestText,
            $generatedTest
        );
    }
}
