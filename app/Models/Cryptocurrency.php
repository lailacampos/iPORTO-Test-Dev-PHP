<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cryptocurrency extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ["symbol", "price", "time"];

    #region public helpers

    public function calculateAveragePrice($symbol)
    {
        $averagePrice = $this->where("symbol", $symbol)
            ->take(100)
            ->avg("price");

        return $averagePrice;
    }

    public function isCurrentPriceLowerThanAverage($cryptocurrency)
    {
        $price = $cryptocurrency["price"];
        $symbol = $cryptocurrency["symbol"];

        $averagePrice = $this->calculateAveragePrice($symbol);

        if ($price < $averagePrice * 0.995) {

            return true;
        } else {
            return false;
        }
    }

    public function saveCryptoToDataBase($cryptocurrency)
    {
        return $this->create(
            [
                "symbol" => $cryptocurrency["symbol"],
                "price" => $cryptocurrency["price"],
                "time" => $cryptocurrency["time"],
            ]
        );
    }
    #endregion
}
