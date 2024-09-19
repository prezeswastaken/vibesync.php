<?php

use App\Actions\RefreshCurrencyRatesAction;
use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    $action = new RefreshCurrencyRatesAction;
    $action->handle();
})->daily();
