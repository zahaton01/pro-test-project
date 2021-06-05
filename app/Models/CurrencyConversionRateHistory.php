<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurrencyConversionRateHistory extends Model
{
    protected $table = 'currency_conversion_rates_history';

    public function sourceCurrency()
    {
        return $this->belongsTo(Currency::class, 'source_currency_id');
    }

    public function outcomeCurrency()
    {
        return $this->belongsTo(Currency::class, 'outcome_currency_id');
    }
}
