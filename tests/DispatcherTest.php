<?php

namespace Neat\Event\Test;

use Neat\Event\Dispatcher;
use Neat\Service\Container;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DispatcherTest extends TestCase
{
    /**
     * @return Listener|MockObject
     */
    private function container(): Container
    {
        return $this->getMockBuilder(Container::class)->getMock();
    }

    /**
     * @return Listener|MockObject
     */
    private function listener(): Listener
    {
        return $this->getMockBuilder(Listener::class)->getMock();
    }

    public function testEmpty()
    {
        $dispatcher = new Dispatcher($this->container());

        $this->assertSame(0, iterator_count($dispatcher->listeners(new Event())));

        $dispatcher->dispatch(new Event());
    }

    public function testDispatchEvent()
    {
        $event = new Event();

        $listener = new Listener();

        $container = $this->container();
        $container
            ->expects($this->once())
            ->method('call')
            ->with([$listener, 'notify'], ['event' => $event]);

        $dispatcher = new Dispatcher($container);
        $dispatcher->listen(Event::class, [$listener, 'notify']);

        $this->assertSame([[$listener, 'notify']], iterator_to_array($dispatcher->listeners($event)));
        $dispatcher->dispatch($event);

        $dispatcher->dispatch((object) []);
    }

    public function testDispatchEventParentChild()
    {
        $event = new EventChild();

        $listener = new Listener();

        $container = $this->container();
        $container
            ->expects($this->once())
            ->method('call')
            ->with([$listener, 'notify'], ['event' => $event]);

        $dispatcher = new Dispatcher($container);
        $dispatcher->listen(EventParent::class, [$listener, 'notify']);

        $this->assertSame([[$listener, 'notify']], iterator_to_array($dispatcher->listeners($event)));
        $dispatcher->dispatch($event);
    }

    public function testDispatchEventInterfaceChild()
    {
        $event = new EventChild();

        $listener = new Listener();

        $container = $this->container();
        $container
            ->expects($this->once())
            ->method('call')
            ->with([$listener, 'notify'], ['event' => $event]);

        $dispatcher = new Dispatcher($container);
        $dispatcher->listen(EventInterface::class, [$listener, 'notify']);

        $this->assertSame([[$listener, 'notify']], iterator_to_array($dispatcher->listeners($event)));
        $dispatcher->dispatch($event);
    }

    public function testDispatchEventWithStop()
    {
        $event = new EventWithStop();

        $listener = new Listener();

        $container = $this->container();
        $container
            ->expects($this->once())
            ->method('call')
            ->with([$listener, 'notify'], ['event' => $event]);

        $dispatcher = new Dispatcher($container);
        $dispatcher->listen(EventWithStop::class, [$listener, 'notify']);

        $dispatcher->dispatch($event);
        $event->stop();
        $dispatcher->dispatch($event);
    }
}
