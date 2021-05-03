<?php

namespace Neat\Event;

use Neat\Log\File;
use Neat\Object\Event;
use Neat\Service\Container;
use Shopbox\Event\Configuration\Log as Configuration;

class LoggingDispatcher extends Dispatcher
{
    private $configuration;

    public function __construct(Container $container, Configuration $configuration)
    {
        parent::__construct($container);
        $this->configuration = $configuration;
    }

    public function listeners(object $event): iterable
    {
        foreach (parent::listeners($event) as $listener) {
            $this->log($event, $listener);
            yield $listener;
        }
    }

    public function dispatch(object $event)
    {
        $this->log($event);
        return parent::dispatch($event);
    }

    private function log(object $event, $listener = null)
    {
        if (!$this->configuration->logEvent($event)) {
            return;
        }

        $logger = new File($this->logPath($event));

        if ($listener !== null) {
            $logger->info("Listener: " . $listener);
        } else {
            $logger->info("\n------------------------------------------------------------------");
            $logger->info("Dispatching event");
            $logger->info("Time:   ".date("Y-m-d H:i:s"));
            $logger->info("Event:  ".get_class($event));
            if ($event instanceof Event) {
                $logger->info("Entity: ".get_class($event->entity()))."#".($event->entity()->id??"NULL");
            }

            $logger->info("\nTrace:");
            foreach (debug_backtrace() as $i => $trace) {
                $logger->info(sprintf(
                    "#%s %s(%s): %s->%s",
                    $i,
                    $trace['file'],
                    $trace['line'],
                    $trace['class'],
                    $trace['function']
                ));
            }
            $logger->info(" ");
        }
    }

    private function logPath(object $event): string
    {
        $path = str_replace('{date}', date('Ymd'), $this->configuration->file);
        $path = str_replace('{event}', str_replace("\\", '-', get_class($event)), $path);

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        return $path;
    }
}
