<?php

namespace App\Console\Commands;

use App\AppCurrency;
use App\Models\Currency;
use App\Models\CurrencyConversionRate;
use App\Models\CurrencyConversionRateHistory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class UpdateCurrencyRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currencies:rates:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates currencies rate table with saving old to history';

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
        $currencies = Currency::all();

        foreach ($currencies as $currency) {
            if ($currency->iso_code === AppCurrency::USD) { // exclude self exchanging
                continue;
            }

            $isoCodes[] = $currency->iso_code;
        }

        $isoCodesAsString = implode(',', $isoCodes ?? []);

        $response = Http::get("{$_ENV['OPEN_EXCHANGER_RATES_API_URL']}/api/latest.json?app_id={$_ENV['OPEN_EXCHANGER_RATES_APP_ID']}&symbols={$isoCodesAsString}");
        $data = json_decode($response->body(), true, 512, JSON_THROW_ON_ERROR);

        $previousRates = CurrencyConversionRate::all();
        foreach ($previousRates as $previousRate) {
            $historyModel = new CurrencyConversionRateHistory();
            $historyModel->source_currency_id = $previousRate->sourceCurrency()->getResults()->id;
            $historyModel->outcome_currency_id = $previousRate->outcomeCurrency()->getResults()->id;
            $historyModel->rate = $previousRate->rate;

            $historyModel->save();
        }

        CurrencyConversionRate::truncate();

        $mappedCurrencies = [];
        foreach ($currencies as $currency) {
            $mappedCurrencies[$currency->iso_code] = $currency->id;
        }

        foreach ($data['rates'] as $isoCode => $rate) {
            $rateModel = new CurrencyConversionRate();
            $rateModel->source_currency_id = $mappedCurrencies[$data['base']];
            $rateModel->outcome_currency_id = $mappedCurrencies[$isoCode] ?? throw new \LogicException('Currency was not found');
            $rateModel->rate = $rate;

            $rateModel->save();
        }
    }
}
