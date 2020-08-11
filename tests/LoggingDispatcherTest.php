<?php

namespace Neat\Event\Test;

use Neat\Event\LoggingDispatcher;
use Neat\Service\Container;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class LoggingDispatcherTest extends TestCase
{
    /**
     * @return Container|MockObject
     */
    private function container(): Container
    {
        return $this->getMockBuilder(Container::class)->getMock();
    }

    public function testDispatch()
    {
        $mock = $this->getMockForAbstractClass(LoggerInterface::class);
        $mock->expects($this->once())
            ->method('debug')
            ->with("[event] Dispatching: Neat\\Event\\Test\\Event Object\n(\n)\n");

        $dispatcher = new LoggingDispatcher($this->container(), $mock);
        $dispatcher->dispatch(new Event());
    }

    public function testListeners()
    {
        $mock = $this->getMockForAbstractClass(LoggerInterface::class);
        $mock->expects($this->once())
            ->method('debug')
            ->with("[event] Listener: TestListener");

        $dispatcher = new LoggingDispatcher($this->container(), $mock);
        $dispatcher->listen(Event::class, 'TestListener');

        foreach ($dispatcher->listeners(new Event()) as $listener) {
        }
    }
}
