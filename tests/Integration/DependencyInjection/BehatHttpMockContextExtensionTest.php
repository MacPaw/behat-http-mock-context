<?php

declare(strict_types=1);

namespace BehatHttpMockContext\Tests\Integration\DependencyInjection;

use BehatHttpMockContext\Collection\ExtendedHttpMockClientCollection;
use BehatHttpMockContext\Context\HttpMockContext;
use BehatHttpMockContext\DependencyInjection\BehatHttpMockContextExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class BehatHttpMockContextExtensionTest extends TestCase
{
    public function testWithDefaultConfig(): void
    {
        $container = $this->createContainerFromFixture('bundle_config');

        $collectionDefinition = $container->getDefinition(ExtendedHttpMockClientCollection::class);
        self::assertSame(ExtendedHttpMockClientCollection::class, $collectionDefinition->getClass());

        $handlersArgument = $collectionDefinition->getArgument('$handlers');
        self::assertInstanceOf(TaggedIteratorArgument::class, $handlersArgument);
        self::assertSame(
            'mock.http_client',
            $handlersArgument->getTag()
        );

        $httpMockContextDefinition = $container->getDefinition(HttpMockContext::class);
        self::assertSame(HttpMockContext::class, $httpMockContextDefinition->getClass());
        self::assertSame(
            'test.service_container',
            (string) $httpMockContextDefinition->getArgument('$container')
        );
        self::assertSame(
            ExtendedHttpMockClientCollection::class,
            (string) $httpMockContextDefinition->getArgument('$extendedHttpMockClientCollection')
        );
    }

    private function createContainerFromFixture(string $fixtureFile): ContainerBuilder
    {
        $container = new ContainerBuilder();

        $container->registerExtension(new BehatHttpMockContextExtension());
        $container->getCompilerPassConfig()->setOptimizationPasses([]);
        $container->getCompilerPassConfig()->setRemovingPasses([]);
        $container->getCompilerPassConfig()->setAfterRemovingPasses([]);

        $this->loadFixture($container, $fixtureFile);

        $container->compile();

        return $container;
    }

    protected function loadFixture(ContainerBuilder $container, string $fixtureFile): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Fixtures'));
        $loader->load($fixtureFile . '.yaml');
    }
}
