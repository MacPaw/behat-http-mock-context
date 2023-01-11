<?php

declare(strict_types=1);

namespace BehatHttpMockContext\Tests\Unit\Collection;

use BehatHttpMockContext\Collection\ExtendedHttpMockClientCollection;
use BehatHttpMockContext\Tests\Unit\AbstractUnitTest;
use TypeError;

class ExtendedMockHttpClientCollectionTest extends AbstractUnitTest
{
    public function testSuccess(): void
    {
        $clientCollection = new ExtendedHttpMockClientCollection([]);

        $this->assertCount(0, $clientCollection->getHttpClients());
    }

    public function testInitFailedParameterType(): void
    {
        $this->expectException(TypeError::class);

        new ExtendedHttpMockClientCollection('string');
    }

    public function testInitFailedHttpClientType(): void
    {
        $this->expectException(TypeError::class);

        new ExtendedHttpMockClientCollection(['string']);
    }

    public function testSetHttpClientsSuccess(): void
    {
        $clientCollection = new ExtendedHttpMockClientCollection([]);

        $this->assertCount(0, $clientCollection->getHttpClients());

        $clientCollection->setHttpClients([
            $this->createHttpClient('test2@test.test')
        ]);

        $this->assertCount(1, $clientCollection->getHttpClients());
    }

    public function testSetHttpClientsFailed(): void
    {
        $this->expectException(TypeError::class);
        $clientCollection = new ExtendedHttpMockClientCollection([]);

        $this->assertCount(0, $clientCollection->getHttpClients());
        $clientCollection->setHttpClients('string');
    }

    public function testResetSuccess(): void
    {
        $clientCollection = new ExtendedHttpMockClientCollection([
            $this->createHttpClient('test1@test.test')
        ]);

        $this->assertCount(1, $clientCollection->getHttpClients());

        $clientCollection->reset();

        $this->assertCount(0, $clientCollection->getHttpClients());
    }
}
