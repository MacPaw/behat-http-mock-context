<?php

declare(strict_types=1);

namespace BehatHttpMockContext\Tests\Unit\Context;

use BehatHttpMockContext\Collection\ExtendedHttpMockClientCollection;
use BehatHttpMockContext\Context\HttpMockContext;
use BehatHttpMockContext\Tests\Unit\AbstractUnitTest;
use RuntimeException;
use stdClass;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpClient\CurlHttpClient;

class MockContextTest extends AbstractUnitTest
{
    public function testFailingObjectInCollection(): void
    {
        $this->expectException(RuntimeException::class);

        $notMockedHttpClient = new CurlHttpClient();
        $mockCollection = new ExtendedHttpMockClientCollection([ $notMockedHttpClient ]);

        $mockContext = new HttpMockContext(
            new Container(),
            $mockCollection
        );

        self::assertCount(1, $mockCollection->getHttpClients());

        $mockContext->afterScenario();
    }

    public function testSuccess(): void
    {
        $client = $this->createHttpClient('http://test.test');
        $httpFixture = $client->createFixture(
            null,
            null,
            null,
            null,
            200,
            'response body'
        );
        $client->addFixture($httpFixture);

        $mockCollection = new ExtendedHttpMockClientCollection([$client]);

        $mockContext = new HttpMockContext(
            new Container(),
            $mockCollection
        );

        self::assertCount(1, $mockCollection->getHttpClients());

        $mockContext->afterScenario();

        self::assertCount(1, $mockCollection->getHttpClients());
    }

    public function testServiceNotFound(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectErrorMessage('Service not found');

        $client = $this->createHttpClient('http://test.test');
        $httpFixture = $client->createFixture(
            null,
            null,
            null,
            null,
            200,
            'response body'
        );
        $client->addFixture($httpFixture);

        $mockCollection = new ExtendedHttpMockClientCollection([ $client ]);

        $mockContext = new HttpMockContext(
            new Container(),
            $mockCollection
        );

        self::assertCount(1, $mockCollection->getHttpClients());

        $mockContext->iMockHttpClientNextResponse('test', 204);
    }

    public function testFailedClientService(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectErrorMessage('You should replace HTTP client service using ExtendedMockHttpClient');

        $client = $this->createHttpClient('http://test.test');
        $httpFixture = $client->createFixture(
            null,
            null,
            null,
            null,
            200,
            'response body'
        );
        $client->addFixture($httpFixture);

        $mockCollection = new ExtendedHttpMockClientCollection([ $client ]);

        $container = new Container();
        $container->set('test', new stdClass());

        $mockContext = new HttpMockContext($container, $mockCollection);

        $mockContext->iMockHttpClientNextResponse('test', 204);
    }
}
