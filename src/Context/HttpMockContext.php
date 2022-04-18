<?php

declare(strict_types=1);

namespace BehatHttpMockContext\Context;

use BehatHttpMockContext\Collection\ExtendedHttpMockClientCollection;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use ExtendedMockHttpClient\ExtendedMockHttpClient;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class HttpMockContext implements Context
{
    public function __construct(
        private ContainerInterface $container,
        private ExtendedHttpMockClientCollection $extendedHttpMockClientCollection
    ) {
    }

    /**
     * After Scenario.
     *
     * @AfterScenario
     */
    public function afterScenario(): void
    {
        foreach ($this->extendedHttpMockClientCollection->getHttpClients() as $extendedMockHttpClient) {
            if (!($extendedMockHttpClient instanceof ExtendedMockHttpClient)) {
                throw new RuntimeException('You should replace HTTP client service using ExtendedMockHttpClient');
            }

            $extendedMockHttpClient->reset();
        }
    }

    /**
     * @param string $httpClientName
     * @param int    $responseStatusCode
     *
     * @Given /^I mock "([^"]*)" HTTP client next response status code should be (\d+)$/
     */
    public function iMockHttpClientNextResponse(
        string $httpClientName,
        int $responseStatusCode
    ): void {
        $this->mockHttpClientNextResponse($httpClientName, $responseStatusCode);
    }

    /**
     * @param string       $httpClientName
     * @param int          $responseStatusCode
     * @param PyStringNode $params
     *
     * @Given /^I mock "([^"]*)" HTTP client next response status code should be (\d+) with body:$/
     */
    public function iMockHttpClientNextResponseWithBody(
        string $httpClientName,
        int $responseStatusCode,
        PyStringNode $params
    ): void {
        $this->mockHttpClientNextResponse($httpClientName, $responseStatusCode, $params);
    }

    /**
     * @param string       $httpClientName
     * @param string       $url
     * @param int          $responseStatusCode
     * @param PyStringNode $params
     *
     * @Given /^I mock "([^"]*)" HTTP client url "([^"]*)" response status code should be (\d+) with body:$/
     */
    public function iMockHttpClientUrlResponse(
        string $httpClientName,
        string $url,
        int $responseStatusCode,
        PyStringNode $params
    ): void {
        $httpClient = $this->getHttpClient($httpClientName);

        $httpFixture = $httpClient->createFixture(
            null,
            sprintf('%s/%s', $httpClient->getBaseUri(), $url),
            null,
            null,
            $responseStatusCode,
            trim($params->getRaw())
        );
        $httpClient->addFixture($httpFixture);
    }

    private function mockHttpClientNextResponse(
        string $httpClientName,
        int $responseStatusCode,
        PyStringNode $params = null
    ): void {
        $httpClient = $this->getHttpClient($httpClientName);
        $body = $params instanceof PyStringNode ? trim($params->getRaw()) : '';

        $httpFixture = $httpClient->createFixture(
            null,
            null,
            null,
            null,
            $responseStatusCode,
            $body
        );
        $httpClient->addFixture($httpFixture);
    }

    private function getHttpClient(string $httpClientName): ExtendedMockHttpClient
    {
        $service = $this->container->has($httpClientName);

        if ($service === false) {
            throw new  RuntimeException('Service not found');
        }

        $httpClient =  $this->container->get($httpClientName);

        if (!($httpClient instanceof ExtendedMockHttpClient)) {
            throw new RuntimeException('You should replace HTTP client service using ExtendedMockHttpClient');
        }

        return $httpClient;
    }
}
