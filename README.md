<h1>Binance's API Consumer</h1>

This is an application for consuming <a href="https://binance-docs.github.io/apidocs">Binance's API</a>.


<h2>Requirements:</h2>

<ul>
    <li>PHP >= 7.4.29</li>
    <li>Laravel 8</li>
    <li>Mysql >= 8.0.29</li>
</ul>

<br>
<h2>Installation:</h2>

<p>CLone or download the project.</p>
<p>Cd into root directory.</p>
<p>Run</p>

```
composer install
```

Set your `DB_DATABASE`, `DB_USERNAME` and `DB_PASSWORD` variables in your  `.env` file to reflect the Database name, user and password you'll use.

<p>Run</p>

```
php artisan migrate
```

Populate the table `cryptocurrencies` with `cryptocurrencies_table.csv` file located inside `data` folder.

<hr>

<h2>How it works:</h2>

<p>This application has no views or routes.</p>
<p>It is composed of two custom Laravel commands and one model:</p>

<ul>
    <li>Commands:
        <ul>
            <li>saveBidPriceOnDataBase</li>
            <li>checkAvgBigPrice</li>
        </ul>
    </li>
    <li>Model:
        <ul>
            <li>Cryptocurrency</li>
        </ul>
    </li>
</ul>

<br>

<h3><strong>saveBidPriceOnDataBase</strong></h3>

<p>Gets latest price for a symbol or symbols from <a href="https://binance-docs.github.io/apidocs/#symbol-price-ticker">Symbol Price Ticker</a> endpoint and saves it to the database.</p>
<p>Prints to console the data saved to the database.</p>

Accepts one optional argument `symbol` that represents the symbol name for a cryptocurrency.

<p>If no argument is passed, gets lastest price of all symbols and saves all to database.</p>
<p>If an argument is passed and the argument is a valid symbol, gets the data for that symbol.</p>
<p>If the symbol is invalid, prints a warning to the console.</p>

<h4><strong>Usage:</strong></h4>

```
php artisan c:saveBidPriceOnDataBase
```

Or

```
php artisan c:saveBidPriceOnDataBase symbol
```

<br>

<h4><strong>Example output:</strong></h4>
<p>Input:</p>

```
php artisan c:saveBidPriceOnDataBase C98USDT
```

<p>Output:</p>

```
Saved to Database:
{"symbol":"C98USDT","price":"0.4842","time":1655219631665,"id":6523}
```

<br>

<h3><strong>checkAvgBigPrice</strong></h3>

<p>Gets latest price for a symbol or symbols from <a href="https://binance-docs.github.io/apidocs/#symbol-price-ticker">Symbol Price Ticker</a> endpoint and checks it against the average price of the last 100 entries of a symbol in the database.</p>

<p>If the current price for a symbol is 0.5% lower than the average price, prints an alert to the console. If the current price for a symbol is equal to or higher than 99.5% of the average corresponding price, prints to the console that the price is Ok</p>

Accepts one optional argument `symbol` that represents the symbol name for a cryptocurrency.

<p>If no argument is passed, gets lastest price of all symbols and checks each one against the average price of the corresponding symbol.</p>

<p>If an argument is passed and the argument is a valid symbol, gets the data for that symbol and checks current price for that symbol against corresponsing average.</p>

<p>If the symbol is invalid, prints a warning to the console.</p>

<br>

<h4><strong>Usage:</strong></h4>

```
php artisan c:checkAvgBigPrice
```

Or

```
php artisan c:checkAvgBigPrice symbol
```
<br>

<h4><strong>Example output:</strong></h4>
<p>Input:</p>

```
php artisan c:checkAvgBigPrice LINAUSDT
```

<p>Output:</p>

```
Current value for LINAUSDT is over 0.5% lower than average value!
```