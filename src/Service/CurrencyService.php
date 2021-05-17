<?php

declare(strict_types=1);

namespace CommissionTask\Service;

use GuzzleHttp\Client;

class CurrencyService
{
    const EUR_CURRENCY = 'EUR';
    private Client $client;

    public function __construct()
    {
        $this->client = new Client(
            [
                'base_uri' => 'http://api.currencylayer.com',
                'timeout' => 2.0,
            ]
        );
    }

    public function getConvertedAmount(string $currencyFrom, string $currencyTo, float $amount, \DateTime $date): float
    {
        if ($currencyFrom === $currencyTo) {
            return $amount;
        }

        $formattedDate = $date->format('Y-m-d');
        if (!isset($_COOKIE[$formattedDate][$currencyFrom]) || !isset($_COOKIE[$formattedDate][$currencyTo])) {
            // had to add query this way didn't work in array
            $responseContent = $this->client->get(
                "/historical?access_key={$_ENV['CURRENCY_CONVERTER_ACCESS_KEY']}&date=$formattedDate&currencies=$currencyFrom,$currencyTo",
            )->getBody()->getContents();

            $_COOKIE[$formattedDate][$currencyTo] = json_decode($responseContent, true)['quotes']["USD$currencyTo"];
            $_COOKIE[$formattedDate][$currencyFrom] = json_decode($responseContent, true)['quotes']["USD$currencyFrom"];
        }

        return $amount / (float)$_COOKIE[$formattedDate][$currencyFrom] * (float)$_COOKIE[$formattedDate][$currencyTo];
    }

    public function getCurrencyFromEuroAmount(string $currencyTo, float $amount, \DateTime $date): float
    {
        return $this->getConvertedAmount(self::EUR_CURRENCY, $currencyTo, $amount, $date);
    }

    public function getEuroFromCurrencyAmount(string $currencyFrom, float $amount, \DateTime $date): float
    {
        return $this->getConvertedAmount($currencyFrom, self::EUR_CURRENCY, $amount, $date);
    }
}
