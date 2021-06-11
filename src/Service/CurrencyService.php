<?php

declare(strict_types=1);

namespace CommissionTask\Service;

use GuzzleHttp\Client;

class CurrencyService
{
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

    public function getConvertedAmount(
        string $currencyFrom,
        string $currencyTo,
        string $amount,
        \DateTime $date
    ): string {
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

        return MoneyCalculator::roundUpMul(
            MoneyCalculator::roundUpDiv(
                $amount,
                (string) $_COOKIE[$formattedDate][$currencyFrom]
            ),
            (string) $_COOKIE[$formattedDate][$currencyTo]
        );
    }

    public function getCurrencyFromDefaultAmount(string $currencyTo, string $amount, \DateTime $date): string
    {
        return $this->getConvertedAmount(getenv('DEFAULT_CURRENCY'), $currencyTo, $amount, $date);
    }

    public function getDefaultFromCurrencyAmount(string $currencyFrom, string $amount, \DateTime $date): string
    {
        return $this->getConvertedAmount($currencyFrom, getenv('DEFAULT_CURRENCY'), $amount, $date);
    }

    public function requestCurrencies(string $formattedDate, string $currencyFrom, string $currencyTo): array
    {
        if ($apiKey = getenv('CURRENCY_CONVERTER_ACCESS_KEY')) {
            // had to add query this way didn't work in array
            return json_decode(
                $response = $this->client->get(
                    "/historical?access_key=$apiKey&date=$formattedDate&currencies=$currencyFrom,$currencyTo",
                )->getBody()->getContents(),
                true
            );
        }

        throw new \Exception('Please add CURRENCY_CONVERTER_ACCESS_KEY in your .env.local file');
    }
}
