<?php

namespace App\Console;

use App\Console\Commands\ConfigureAppCurrencies;
use App\Console\Commands\UpdateCurrencyRates;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ConfigureAppCurrencies::class,
        UpdateCurrencyRates::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('currencies:rates:update')->everyThirtyMinutes();
    }
}
