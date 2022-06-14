<?php

namespace App\Console\Commands;

use App\Models\Cryptocurrency;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SaveBidPriceOnDataBaseCommand extends Command
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

            if ($response->status() === 400) {
                $this->info("Ivalid Symbol");
            } else {
                $cryptocurrency = $response->json();

                $result = $this->cryptocurrency->saveCryptoToDataBase($cryptocurrency);

                $this->info("Saved to Database:");
                $this->info(json_encode($result));
            }
        } else {

            $response = Http::get("https://testnet.binancefuture.com/fapi/v1/ticker/price");

            $cryptocurrencies = $response->json();

            $crypto_list = [];
            foreach ($cryptocurrencies as $cryptocurrency) {
                $result = $this->cryptocurrency->saveCryptoToDataBase($cryptocurrency);
                $result ? $crypto_list[] =  $cryptocurrency : null;
            }

            $this->info("Saved to Database:");
            foreach ($crypto_list as $cryptocurrency) {
                $this->info(json_encode($cryptocurrency));
            }
        }
    }
}
