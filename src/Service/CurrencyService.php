<?php

declare(strict_types=1);

namespace CommissionTask\Service;

use GuzzleHttp\Client;

class CurrencyService
{
    public const EUR_CURRENCY = 'EUR';

    /** @var Client */
    private $client;

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
            $responseContent = $this->requestCurrencies($formattedDate, $currencyFrom, $currencyTo);

            if ($responseContent['success']) {
                $_COOKIE[$formattedDate][$currencyTo] = $responseContent['quotes']["USD$currencyTo"];
                $_COOKIE[$formattedDate][$currencyFrom] = $responseContent['quotes']["USD$currencyFrom"];
            } elseif ($responseContent['error'] && $responseContent['error']['info']) {
                throw new \Exception("Problem with api: {$responseContent['error']['info']}");
            } else {
                throw new \Exception('Unexpected problem with api.');
            }
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

    public function requestCurrencies(string $formattedDate, string $currencyFrom, string $currencyTo)
    {
        if ($apiKey = getenv('CURRENCY_CONVERTER_ACCESS_KEY')) {
            // had to add query this way didn't work in array
            return json_decode(
                $response = $this->client->get(
                    "/historical?access_key=$apiKey&date=$formattedDate&currencies=$currencyFrom,$currencyTo",
                )->getBody()->getContents()
                ,
                true
            );
        }

        throw new \Exception('Please add CURRENCY_CONVERTER_ACCESS_KEY in your .env.local file');
    }
}
