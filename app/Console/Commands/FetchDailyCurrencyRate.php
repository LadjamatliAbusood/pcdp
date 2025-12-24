<?php

namespace App\Console\Commands;

use App\Models\CurrencyRateModel;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
class FetchDailyCurrencyRate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
   protected $signature = 'currency:fetch-daily';

    /**
     * The console command description.
     *
     * @var string
     */
      protected $description = 'Fetch daily exchange rates for all currencies to PHP';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
     

        if(
            CurrencyRateModel::whereDate('rate_date', $today)
                ->where('from_currency', 'MYR')
                ->where('to_currency', 'PHP')
                ->exists()
        ) {
            $this->info('Rate already exists for today.');
            return;
        }
            $response = Http::get('https://api.getgeoapi.com/v2/currency/convert', [
            'api_key' => config('services.getgeoapi.key'),
            'from' => 'MYR',
            'to' => 'PHP',
            'amount' => 1,
            'format' => 'json'
        ]);

        $rate = $response['rates']['PHP']['rate'] ?? null;

        if (!$rate) {
            $this->error('Failed to fetch rate');
            return;
        }

        CurrencyRateModel::create([
            'from_currency' => 'MYR',
            'to_currency' => 'PHP',
            'rate' => $rate,
            'rate_date' => $today,
        ]);

        $this->info("Saved MYR → PHP rate: {$rate}");

        return Command::SUCCESS;
    }
}
