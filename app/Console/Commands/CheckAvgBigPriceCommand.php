<?php

namespace App\Console\Commands;

use App\Models\Cryptocurrency;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CheckAvgBigPriceCommand extends Command
{
    public $cryptocurrency;

    public function __construct(Cryptocurrency $cryptocurrency)
    {
        parent::__construct();
        $this->cryptocurrency = $cryptocurrency;
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'c:checkAvgBigPrice {symbol?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks the average price on the database and informs if the last fecthed price is lower than 0.5% of the average price';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $symbol = $this->argument("symbol");

        if ($symbol) {
            $response = Http::get("https://testnet.binancefuture.com/fapi/v1/ticker/price", [
                "symbol" => $symbol,
            ]);

            if ($response->status() === 400) {
                $this->info("Ivalid Symbol");
            } else {
                $cryptocurrency = $response->json();

                $result = $this->cryptocurrency->isCurrentPriceLowerThanAverage($cryptocurrency);

                $result ?
                    $this->info("Current value for " . $cryptocurrency["symbol"] . " is over 0.5% lower than average value!")
                    :  $this->info("Current value for " . $cryptocurrency["symbol"] . " is ok");
            }
        } else {

            $response = Http::get("https://testnet.binancefuture.com/fapi/v1/ticker/price");

            $cryptocurrencys = $response->json();

            foreach ($cryptocurrencys as $cryptocurrency) {

                $result = $this->cryptocurrency->isCurrentPriceLowerThanAverage($cryptocurrency);

                $result ?
                    $this->info("Current value for " . $cryptocurrency["symbol"] . " is over 0.5% lower than average value!")
                    :  $this->info("Current value for " . $cryptocurrency["symbol"] . " is ok");
            }
        }
    }
}
