<?php

namespace App\Console\Commands;

use App\Models\Cryptocurrency;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SaveBidPriceOnDataBaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'c:saveBidPriceOnDataBase {symbol?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Saves price data in the database based on the entered cryptocurrency';

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

            $this->saveCryptoToDataBase($cryptocurrency);

            $this->info(Cryptocurrency::find($cryptocurrency["symbol"]));
        } else {

            $response = Http::get("https://testnet.binancefuture.com/fapi/v1/ticker/price");

            $cryptocurrencies = $response->json();

            foreach ($cryptocurrencies as $cryptocurrency) {

                $this->saveCryptoToDataBase($cryptocurrency);
            }
        }
    }

    private function saveCryptoToDataBase($cryptocurrency)
    {

        Cryptocurrency::create(
            [
                "symbol" => $cryptocurrency["symbol"],
                "price" => $cryptocurrency["price"],
                "time" => $cryptocurrency["time"],
            ]
        );
    }
}
