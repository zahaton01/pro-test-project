<?php

namespace App\Http\Controllers\Api;

use App\AppCurrency;
use App\Models\Currency;
use App\Models\CurrencyConversionRate;
use Illuminate\Support\Facades\Cache;
use Laravel\Lumen\Http\Request;
use Laravel\Lumen\Routing\Controller;

class CurrenciesController extends Controller
{
    public function exchange(Request $request)
    {
        $sourceIso = $request->json()->get('source');
        $outcomeIso = $request->json()->get('outcome');
        $amount = $request->json()->get('amount');

        if ($sourceIso !== AppCurrency::USD) {
            throw new \InvalidArgumentException('Application supports only USD as the base currency');
        }

        if (!Cache::has($sourceIso)) {
            $sourceCurrency = Currency::where('iso_code', $sourceIso)->first();

            if (null === $sourceCurrency) {
                throw new \Exception('Source currency was not found');
            }

            Cache::put($sourceIso, $sourceCurrency->id, 3600);
        }

        if (!Cache::has($outcomeIso)) {
            $outcomeCurrency = Currency::where('iso_code', $outcomeIso)->first();

            if (null === $outcomeCurrency) {
                throw new \Exception('Outcome currency was not found');
            }

            Cache::put($outcomeIso, $outcomeCurrency->id, 3600);
        }

        $conversionRateKey = "{$sourceIso}_{$outcomeIso}";
        if (!Cache::has($conversionRateKey)) {
            $conversionRate = CurrencyConversionRate::where(['source_currency_id' => Cache::get($sourceIso), 'outcome_currency_id' => Cache::get($outcomeIso)])->first();
            if (null === $conversionRate) {
                throw new \Exception('Conversion rate was not found');
            }

            Cache::put($conversionRateKey, $conversionRate->rate, 1800);
        }

        return response()->json(['result' => round($amount * Cache::get($conversionRateKey), 2)]);
    }
}
