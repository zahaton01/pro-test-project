<?php

namespace App;

interface AppCurrency
{
    public const USD = 'USD';
    public const EUR = 'EUR';
    public const UAH = 'UAH';

    public const ALL = [
        self::UAH,
        self::USD,
        self::EUR
    ];
}
