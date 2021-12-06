<?php

declare(strict_types=1);

namespace BehatHttpMockContext\Context;

use BehatHttpMockContext\Collection\ExtendedMockHttpClientCollection;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use ExtendedMockHttpClient\Builder\RequestMockBuilder;
use ExtendedMockHttpClient\ExtendedMockHttpClient;
use ExtendedMockHttpClient\Model\HttpFixture;
use RuntimeException;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpKernel\KernelInterface;

class MockContext implements Context
{
    private KernelInterface $kernel;
    private ExtendedMockHttpClientCollection $extendedMockHttpClientCollection;

    public function __construct(
        KernelInterface $kernel,
        ExtendedMockHttpClientCollection $extendedMockHttpClientCollection
    ) {
        $this->kernel = $kernel;
        $this->extendedMockHttpClientCollection = $extendedMockHttpClientCollection;
    }

    /**
     * After Scenario.
     *
     * @AfterScenario
     */
    public function afterScenario(): void
    {
        foreach ($this->extendedMockHttpClientCollection->getHandlers() as $extendedMockHttpClient) {
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

        $httpClient->addFixture(new HttpFixture(
            (new RequestMockBuilder())
                ->urlEquals(sprintf('%s/%s', $httpClient->getBaseUri(), $url))
                ->build(),
            new MockResponse(trim($params->getRaw()), [
                'http_code' => $responseStatusCode
            ])
        ));
    }

    private function mockHttpClientNextResponse(
        string $httpClientName,
        int $responseStatusCode,
        PyStringNode $params = null
    ): void {
        $httpClient = $this->getHttpClient($httpClientName);
        $body = $params instanceof PyStringNode ? trim($params->getRaw()) : '';

        $httpClient->addFixture(new HttpFixture(
            (new RequestMockBuilder())->build(),
            new MockResponse(
                $body,
                ['http_code' => $responseStatusCode]
            )
        ));
    }

    private function getHttpClient(string $httpClientName): object
    {
        $httpClient = $this->getService($httpClientName);

        if (!($httpClient instanceof ExtendedMockHttpClient)) {
            throw new RuntimeException('You should replace HTTP client service using ExtendedMockHttpClient');
        }

        return $httpClient;
    }

    private function getService(string $id): object
    {
        return $this->kernel->getContainer()->get('test.service_container')->get($id);
    }
}
