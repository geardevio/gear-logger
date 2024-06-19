<?php

namespace GearDev\Logger\Warmers;

use GearDev\Core\Attributes\Warmer;
use GearDev\Core\ContextStorage\ContextStorage;
use GearDev\Core\Warmers\WarmerInterface;
use GearDev\Coroutines\Co\ChannelFactory;
use Illuminate\Foundation\Application;

#[Warmer]
class LoggerWarmer implements WarmerInterface
{

    public function warm(Application $app): void
    {
        $channel = ChannelFactory::createChannel(1000);
        ContextStorage::setSystemChannel('log', $channel);
    }
}
