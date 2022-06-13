<?php

namespace App\Console\Commands;

use App\Models\Cryptocurrency;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CheckAvgBigPriceCommand extends Command
{
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
    protected $description = 'Checks the average price on the database and informs if the last fecthed price is less than 0.5% of the average price';

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

            $cryptocurrency = $response->json();

            $this->showPriceCheckInfo($cryptocurrency);
        } else {

            $response = Http::get("https://testnet.binancefuture.com/fapi/v1/ticker/price");

            $cryptocurrencies = $response->json();

            foreach ($cryptocurrencies as $cryptocurrency) {

                $this->showPriceCheckInfo($cryptocurrency);
            }
        }
    }

    private function calculateAveragePrice($symbol)
    {
        $averagePrice = Cryptocurrency::where("symbol", $symbol)
            ->take(100)
            ->avg("price");

        return $averagePrice;
    }

    private function isCurrentPriceLowerThanAverage($cryptocurrency)
    {
        $price = $cryptocurrency["price"];
        $symbol = $cryptocurrency["symbol"];

        $averagePrice = $this->calculateAveragePrice($symbol);

        if ($price < $averagePrice * /*0.995*/ 1) {

            return true;
        } else {
            return false;
        }
    }

    private function showPriceCheckInfo($cryptocurrency)
    {
        if ($this->isCurrentPriceLowerThanAverage($cryptocurrency)) {

            $this->info("Current value for " . $cryptocurrency["symbol"] . " is over 0.5% lower than average value!");
        } else {

            $this->info("Current value for " . $cryptocurrency["symbol"] . " is ok");
        }
    }
}
