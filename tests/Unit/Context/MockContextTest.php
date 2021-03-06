<?php

declare(strict_types=1);

namespace BehatHttpMockContext\Tests\Unit\Context;

use BehatHttpMockContext\Collection\ExtendedHttpMockClientCollection;
use BehatHttpMockContext\Context\HttpMockContext;
use ExtendedMockHttpClient\Builder\RequestMockBuilder;
use ExtendedMockHttpClient\ExtendedMockHttpClient;
use ExtendedMockHttpClient\Model\HttpFixture;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpClient\Response\MockResponse;

class MockContextTest extends TestCase
{
    public function testFailingObjectInCollection(): void
    {
        $this->expectException(RuntimeException::class);

        $mockCollection = new ExtendedHttpMockClientCollection(
            ['string']
        );

        $mockContext = new HttpMockContext(
            new Container(),
            $mockCollection
        );

        self::assertCount(1, $mockCollection->getHandlers());

        $mockContext->afterScenario();
    }

    public function testSuccess(): void
    {
        $client = new ExtendedMockHttpClient('http://test.test');
        $client->addFixture(new HttpFixture(
            (new RequestMockBuilder())->build(),
            new MockResponse('response body', [
                'http_code' => 200
            ])
        ));

        $mockCollection = new ExtendedHttpMockClientCollection([
            new ExtendedMockHttpClient('macpaw.com')
        ]);

        $mockContext = new HttpMockContext(
            new Container(),
            $mockCollection
        );

        self::assertCount(1, $mockCollection->getHandlers());

        $mockContext->afterScenario();

        self::assertCount(1, $mockCollection->getHandlers());
    }

    public function testServiceNotFound(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectErrorMessage('Service not found');
        $client = new ExtendedMockHttpClient('http://test.test');
        $client->addFixture(new HttpFixture(
            (new RequestMockBuilder())->build(),
            new MockResponse('response body', [
                'http_code' => 200
            ])
        ));

        $mockCollection = new ExtendedHttpMockClientCollection([
           new ExtendedMockHttpClient('macpaw.com')
        ]);

        $mockContext = new HttpMockContext(
            new Container(),
            $mockCollection
        );

        self::assertCount(1, $mockCollection->getHandlers());

        $mockContext->iMockHttpClientNextResponse('test', 204);
    }

    public function testFailedClientService(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectErrorMessage('You should replace HTTP client service using ExtendedMockHttpClient');

        $client = new ExtendedMockHttpClient('http://test.test');
        $client->addFixture(new HttpFixture(
            (new RequestMockBuilder())->build(),
            new MockResponse('response body', [
                'http_code' => 200
            ])
        ));

        $mockCollection = new ExtendedHttpMockClientCollection([
           new ExtendedMockHttpClient('macpaw.com')
        ]);

        $container = new Container();
        $container->set('test', new stdClass());

        $mockContext = new HttpMockContext($container, $mockCollection);

        $mockContext->iMockHttpClientNextResponse('test', 204);
    }
}
