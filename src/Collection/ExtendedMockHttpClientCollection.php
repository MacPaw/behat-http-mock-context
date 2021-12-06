<?php

declare(strict_types=1);

namespace BehatHttpMockContext\Collection;

use Symfony\Component\Cache\ResettableInterface;

class ExtendedMockHttpClientCollection implements ResettableInterface
{
    /*** @param iterable $handlers */
    public function __construct(private iterable $handlers)
    {
    }

    public function getHandlers(): iterable
    {
        return $this->handlers;
    }

    public function setHandlers(iterable $handlers): void
    {
        $this->handlers = $handlers;
    }

    public function reset(): void
    {
        $this->handlers = [];
    }
}
