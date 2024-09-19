<?php

namespace App\Console\Commands;

use App\Actions\RefreshCurrencyRatesAction;
use Illuminate\Console\Command;

use function Laravel\Prompts\info;

class RefreshCurrencyRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stocks:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches the latest currency rates from the API and updates current currencies in database.';

    /**
     * Execute the console command.
     */
    public function handle(RefreshCurrencyRatesAction $action)
    {
        info('Fetching the latest currency rates from the API...');
        $action->handle();
        info('Done!');
    }
}
