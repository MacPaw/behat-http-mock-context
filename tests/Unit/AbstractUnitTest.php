<?php

declare(strict_types=1);

namespace BehatHttpMockContext\Tests\Unit;

use ExtendedMockHttpClient\ExtendedMockHttpClient;
use ExtendedMockHttpClient\Factory\HttpFixtureBuilderFactory;
use ExtendedMockHttpClient\Factory\HttpFixtureFactory;
use PHPUnit\Framework\TestCase;

abstract class AbstractUnitTest extends TestCase
{
    protected function createHttpClient(string $baseUri): ExtendedMockHttpClient
    {
        $httpFixtureFactory = new HttpFixtureFactory([
            '' => [ 'request', 'response' ],
            'request' => [ 'url' ],
        ]);
        $httpFixtureBuilderFactory = new HttpFixtureBuilderFactory($httpFixtureFactory);

        return new ExtendedMockHttpClient($baseUri, $httpFixtureBuilderFactory);
    }
}
