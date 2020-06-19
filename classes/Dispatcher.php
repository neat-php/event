<?php

namespace Neat\Event;

use Neat\Service\Container;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\StoppableEventInterface;

class Dispatcher implements EventDispatcherInterface
{
    /** @var Container */
    private $container;

    /** @var callable[][] */
    private $listeners = [];

    /**
     * Dispatcher constructor
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Listen for events of a given class or interface
     *
     * @param string   $class
     * @param callable $listener
     */
    public function listen(string $class, $listener)
    {
        $this->listeners[$class][] = $listener;
    }

    /**
     * Get types for a given event object
     *
     * @param object $event
     * @return iterable|string[]
     */
    private function types(object $event): iterable
    {
        yield get_class($event);
        yield from class_parents($event);
        yield from class_implements($event);
    }

    /**
     * Get listeners for a given event object
     *
     * @param object $event
     * @return iterable|callable[]
     */
    public function listeners(object $event): iterable
    {
        foreach ($this->types($event) as $type) {
            yield from $this->listeners[$type] ?? [];
        }
    }

    /**
     * Dispatch event to all listeners
     *
     * @param object $event
     * @return object
     */
    public function dispatch(object $event)
    {
        foreach ($this->listeners($event) as $listener) {
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                break;
            }
            $this->container->call($listener, ['event' => $event]);
        }

        return $event;
    }
}
