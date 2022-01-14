<?php

declare(strict_types=1);

namespace BehatHttpMockContext\Tests\Collection;

use BehatHttpMockContext\Collection\ExtendedHttpMockClientCollection;
use PHPUnit\Framework\TestCase;
use TypeError;

class ExtendedMockHttpClientCollectionTest extends TestCase
{
    public function testSuccess(): void
    {
        $clientCollection = new ExtendedHttpMockClientCollection([]);

        $this->assertCount(0, $clientCollection->getHandlers());
    }

    public function testInitFailed(): void
    {
        $this->expectException(TypeError::class);
        new ExtendedHttpMockClientCollection('string');
    }

    public function testSetHandlersSuccess(): void
    {
        $clientCollection = new ExtendedHttpMockClientCollection([]);

        $this->assertCount(0, $clientCollection->getHandlers());

        $clientCollection->setHandlers([[]]);

        $this->assertCount(1, $clientCollection->getHandlers());
    }

    public function testSetHandlersFailed(): void
    {
        $this->expectException(TypeError::class);
        $clientCollection = new ExtendedHttpMockClientCollection([]);

        $this->assertCount(0, $clientCollection->getHandlers());
        $clientCollection->setHandlers('string');
    }

    public function testResetSuccess(): void
    {
        $clientCollection = new ExtendedHttpMockClientCollection([[]]);

        $this->assertCount(1, $clientCollection->getHandlers());

        $clientCollection->reset();

        $this->assertCount(0, $clientCollection->getHandlers());
    }
}
