<?php

namespace GearDev\Logger\Warmers;

use GearDev\Core\Attributes\Warmer;
use GearDev\Core\ContextStorage\ContextStorage;
use GearDev\Core\Warmers\WarmerInterface;
use GearDev\Coroutines\Co\ChannelFactory;
use GearDev\Logger\Logger\CustomLogger;
use GearDev\Masko\Formatter\ValuesMaskJsonFormatter;
use Illuminate\Foundation\Application;

#[Warmer]
class LoggerWarmer implements WarmerInterface
{

    public function warm(Application $app): void
    {
        $channel = ChannelFactory::createChannel(1000);
        ContextStorage::setSystemChannel('log', $channel);
        config(['logging.channels.custom'=> [
            'driver' => 'custom',
            'level'=>env('LOG_LEVEL', 'error'),
            'via' => CustomLogger::class,
        ]]);
        if (class_exists(ValuesMaskJsonFormatter::class)) {
            config(['logging.channels.stderr.formatter'=>env('LOG_STDERR_FORMATTER', ValuesMaskJsonFormatter::class)]);
        }
    }
}