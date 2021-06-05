<?php

namespace App\Console\Commands;

use App\AppCurrency;
use App\Models\Currency;
use Illuminate\Console\Command;

class ConfigureAppCurrencies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currencies:configure';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates currencies table with currencies which the app is using';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        foreach (AppCurrency::ALL as $isoCode) {
            $currency = new Currency;
            $currency->iso_code = $isoCode;
            $currency->save();
        }
    }
}
