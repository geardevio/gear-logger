<?php

namespace GearDev\Logger\Processes;

use GearDev\Core\ContextStorage\ContextStorage;
use GearDev\Coroutines\Co\ChannelFactory;
use GearDev\Coroutines\Co\CoFactory;
use GearDev\Logger\Logger\CustomLogger;
use GearDev\Logger\Message\LogMessage;
use GearDev\Masko\Processes\ValuesMaskJsonFormatter;
use GearDev\Processes\Attributes\Process;
use GearDev\Processes\ProcessesManagement\AbstractProcess;
use Illuminate\Support\Facades\Log;

#[Process(processName: 'logger-process', serverOnly: false)]
class LoggerProcess extends AbstractProcess
{

    protected function run(): bool
    {
        if (config('logging.default', 'custom')=='custom') {
            config(['logging.channels.custom'=> [
                'driver' => 'custom',
                'level'=>env('LOG_LEVEL', 'error'),
                'via' => CustomLogger::class,
            ]]);
            if (class_exists(ValuesMaskJsonFormatter::class)) {
                config(['logging.channels.stderr.formatter'=>env('LOG_STDERR_FORMATTER', ValuesMaskJsonFormatter::class)]);
            }
            config(['logging.default'=>'custom']);
        }
        $channel = ChannelFactory::createChannel(1000);
        ContextStorage::setSystemChannel('log', $channel);
        if (config('logging.default', 'custom')=='custom') {
            $driver = 'stderr';
        } else {
            $driver = config('logging.default', 'stderr');
        }
        CoFactory::createCo('main-logger')
            ->charge(function($channel, $logChannel) {
                /** @var LogMessage $message */
                while ($message = $channel->pop()) {
                    if ($message->severity===null) $message->severity='alert';
                    Log
                        ::driver($logChannel)->log(
                            $message->severity,
                            $message->message,
                            $message->context
                        );
                }
            })->args($channel, $driver)->runWithClonedDiContainer();
        return true;
    }
}