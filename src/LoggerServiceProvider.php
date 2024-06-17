<?php

namespace GearDev\Logger;

use GearDev\Collector\Collector\Collector;
use Illuminate\Support\ServiceProvider;

class LoggerServiceProvider extends ServiceProvider
{
    public function boot() {

    }

    public function register() {
        Collector::addPackageToCollector(__DIR__);
    }
}