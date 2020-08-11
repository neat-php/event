<?php

namespace Neat\Event;

use Neat\Service\Container;
use Psr\Log\LoggerInterface;

class LoggingDispatcher extends Dispatcher
{
    private $logger;

    public function __construct(Container $container, LoggerInterface $logger)
    {
        parent::__construct($container);
        $this->logger = $logger;
    }

    public function listeners(object $event): iterable
    {
        foreach (parent::listeners($event) as $listener) {
            $this->logger->debug("[event] Listener: " . (is_string($listener) ? $listener : print_r($listener, true)));
            yield $listener;
        }
    }

    public function dispatch(object $event)
    {
        $this->logger->debug("[event] Dispatching: " . print_r($event, true));
        return parent::dispatch($event);
    }
}
