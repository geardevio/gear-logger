<?php

namespace GearDev\Logger\Processes;

use GearDev\Core\ContextStorage\ContextStorage;
use GearDev\Coroutines\Co\CoFactory;
use GearDev\Logger\Message\LogMessage;
use GearDev\Processes\Attributes\Process;
use GearDev\Processes\ProcessesManagement\AbstractProcess;
use Illuminate\Support\Facades\Log;

#[Process(processName: 'logger-process', serverOnly: false)]
class LoggerProcess extends AbstractProcess
{

    protected function run(): bool
    {
        CoFactory::createCo('main-logger')
            ->charge(function() {
                $channel = ContextStorage::getSystemChannel('log');
                $logFinalChannel = config('gear.logger.logFinalChannel', env('LOG_FINAL_CHANNEL', 'stderr'));
                /** @var LogMessage $message */
                while ($message = $channel->pop()) {
                    if ($message->severity===null) $message->severity='alert';
                    Log
                        ::driver($logFinalChannel)->log(
                            $message->severity,
                            $message->message,
                            $message->context
                        );
                }
            })->runWithClonedDiContainer();
        return true;
    }
}
