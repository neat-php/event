Neat Event components
=======================
[![Stable Version](https://poser.pugx.org/neat/event/version)](https://packagist.org/packages/neat/event)
[![Build Status](https://travis-ci.org/neat-php/event.svg?branch=master)](https://travis-ci.org/neat-php/event)
[![codecov](https://codecov.io/gh/neat-php/event/branch/master/graph/badge.svg)](https://codecov.io/gh/neat-php/event)

Neat Event components provide a clean and expressive API for your application
to dispatch and listen for events.

Getting started
---------------
To install this package, simply issue [composer](https://getcomposer.org) on the
command line:
```
composer require neat/event
```

The event dispatcher can be created with a few lines of code.
```php
<?php
// First create your container (used for event dispatching and dependency injection)
$container = new Neat\Service\Container();

// The dispatcher will need a service container for dependency injection
$dispatcher = new Neat\Event\Dispatcher($container);
```

Defining events
---------------
```php
<?php

// A generic event can be defined using a simple PHP class without any members
class SomeEvent
{
}

// Specific events may also use inheritance using extends or interfaces
class SomeSpecificEvent extends SomeEvent
{
}

// Implement the Stoppable event interface if you want control over dispatching your event
class SomeStoppableEvent implements Neat\Event\Stoppable
{
    public function isPropagationStopped(): bool
    {
        return random_int(0, 1) == 1;
    }
}
```

Listen for events
-----------------
```php
<?php

// Now listen for events of this class or any of its subclasses or implementations
$dispatcher->listen(SomeEvent::class, function (SomeEvent $event) {
    // ...
});

// Or for a specific event
$dispatcher->listen(SomeSpecificEvent::class, function (SomeSpecificEvent $event) {
    // ...
});
```

Dispatch an event
-----------------
Now we're ready to dispatch a new event
```php
<?php

// First create an instance of your event
$event = new Event();

// Then let the dispatcher call all the appropriate listeners
$dispatcher->dispatch($event);
```
