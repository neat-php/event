<?php

namespace Neat\Event;

use Closure;
use Neat\Log\File;
use Neat\Object\Event;
use Neat\Service\Container;
use Psr\Log\LoggerInterface;

class LoggingDispatcher extends Dispatcher
{
    /** @var string */
    protected $logFile;

    public function __construct(Container $container, string $logFile)
    {
        parent::__construct($container);
        $this->logFile = $logFile;
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

    /**
     * @param object              $event
     * @param Closure|string|null $listener
     * @return void
     */
    protected function log(object $event, $listener = null)
    {
        $logger = $this->getLogger($event);

        if ($listener !== null) {
            $logger->info("Listener: " . $listener);
        } else {
            $logger->info("\n------------------------------------------------------------------");
            $logger->info("Dispatching event");
            $logger->info("Time:   " . date("Y-m-d H:i:s"));
            $logger->info("Event:  " . get_class($event));
            if ($event instanceof Event) {
                $logger->info("Entity: " . get_class($event->entity()) . "#" . ($event->entity()->id ?? "NULL"));
            }

            $logger->info("\nTrace:");
            foreach (debug_backtrace() as $i => $trace) {
                $message = sprintf(
                    "#%s %s(%s): %s->%s",
                    $i,
                    $trace['file'] ?? "",
                    $trace['line'] ?? "",
                    $trace['class'] ?? "",
                    isset($trace['function']) ? $trace['function'] . "()" : ""
                );
                $logger->info($message);
            }
            $logger->info(" ");
        }
    }

    private function logPath(object $event): string
    {
        $path = str_replace('{date}', date('Ymd'), $this->logFile);
        $path = str_replace('{event}', str_replace("\\", '-', get_class($event)), $path);

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        return $path;
    }

    /**
     * @param object $event
     * @return LoggerInterface
     */
    private function getLogger(object $event): LoggerInterface
    {
        return new File($this->logPath($event));
    }
}
