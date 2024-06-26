<?php

namespace GearDev\Logger\Logger;

use GearDev\Logger\Handler\CoroutineTolerantHandler;
use Monolog\Level;
use Monolog\Logger;

class CustomLogger
{
    /**
     * Create a custom Monolog instance.
     */
    public function __invoke(array $config): Logger
    {
        $logger = new Logger(
            config('app.name'),
            [
                new CoroutineTolerantHandler($config['level'] ?? Level::Debug),
            ]
        );
        $logger->useLoggingLoopDetection(false);
        return $logger;
    }
}
