<?php

namespace GearDev\Logger\Handler;

use GearDev\Core\ContextStorage\ContextStorage;
use GearDev\Logger\Message\LogMessage;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;

class CoroutineTolerantHandler extends AbstractProcessingHandler
{
    protected function write(LogRecord $record): void
    {
        $severity = $record->level->getName();
        $message = $record->message;
        $context = $record->context ?? [];
        $context['ProcessName'] = ContextStorage::getCurrentRoutineName();
        $context = array_merge($context, ContextStorage::getLogContext());
        ContextStorage::getSystemChannel('log')->push(
            new LogMessage(
                $severity,
                $message,
                $context
            )
        );
    }
}
