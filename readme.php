<?php

// This file helps with inspection errors in readme.md.

/** @noinspection PhpUnused */
/** @noinspection PhpUndefinedClassInspection */

$container = new Neat\Service\Container();

$dispatcher = new Neat\Event\Dispatcher($container);

class SomeEvent
{
}

class SomeSpecificEvent extends SomeEvent
{
}

class SomeStoppableEvent implements Neat\Event\Stoppable
{
    public function isPropagationStopped(): bool
    {
        return random_int(0, 1) == 1;
    }
}
