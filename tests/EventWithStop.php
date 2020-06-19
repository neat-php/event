<?php

namespace Neat\Event\Test;

use Neat\Event\Stoppable;

class EventWithStop implements Stoppable
{
    private $stopped = false;

    public function stop()
    {
        $this->stopped = true;
    }

    public function isPropagationStopped(): bool
    {
        return $this->stopped;
    }
}
