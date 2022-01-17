<?php

declare(strict_types=1);

namespace BehatHttpMockContext\Tests\Integration\DependencyInjection;

use PHPUnit\Framework\TestCase;
use BehatHttpMockContext\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

final class ConfigurationTest extends TestCase
{
    public function testProcessConfigurationWithDefaultConfiguration(): void
    {
        $expectedBundleDefaultConfig = [];

        $this->assertSame($expectedBundleDefaultConfig, $this->processConfiguration([]));
    }

    private function processConfiguration(array $values): array
    {
        $processor = new Processor();

        return $processor->processConfiguration(new Configuration(), ['behat_http_mock_context' => $values]);
    }
}
