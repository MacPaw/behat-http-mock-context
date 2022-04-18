<?php

declare(strict_types=1);

namespace BehatHttpMockContext\Collection;

use Symfony\Component\Cache\ResettableInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ExtendedHttpMockClientCollection implements ResettableInterface
{
    /**
     * @var HttpClientInterface[]
     */
    private array $httpClients = [];

    /**
     * @param HttpClientInterface[] $httpClients
     */
    public function __construct(iterable $httpClients)
    {
        $this->setHttpClients($httpClients);
    }

    /**
     * @param HttpClientInterface[] $httpClients
     */
    public function setHttpClients(iterable $httpClients): void
    {
        foreach ($httpClients as $httpClient) {
            $this->addHttpClient($httpClient);
        }
    }

    public function addHttpClient(HttpClientInterface $httpClient): void
    {
        $this->httpClients[] = $httpClient;
    }

    /**
     * @return HttpClientInterface[]
     */
    public function getHttpClients(): iterable
    {
        return $this->httpClients;
    }

    public function reset(): void
    {
        $this->httpClients = [];
    }
}
