<?php

declare(strict_types=1);

namespace CommissionTask\Service;

use GuzzleHttp\Client;

class CurrencyService
{
    private Client $client;
    private array $store = [];

    public function __construct(Client $client)
    {
        $this->client = $client;
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
        if (!isset($this->store[$formattedDate][$currencyFrom]) || !isset($this->store[$formattedDate][$currencyTo])) {
            $responseContent = $this->requestCurrencies($formattedDate, $currencyFrom, $currencyTo);

            if ($responseContent['success']) {
                $this->store[$formattedDate][$currencyTo] = $responseContent['quotes']["USD$currencyTo"];
                $this->store[$formattedDate][$currencyFrom] = $responseContent['quotes']["USD$currencyFrom"];
            } elseif ($responseContent['error'] && $responseContent['error']['info']) {
                throw new \Exception("Problem with api: {$responseContent['error']['info']}");
            } else {
                throw new \Exception('Unexpected problem with api.');
            }
        }

        return MoneyCalculator::roundUpMul(
            MoneyCalculator::roundUpDiv(
                $amount,
                (string) $this->store[$formattedDate][$currencyFrom]
            ),
            (string) $this->store[$formattedDate][$currencyTo]
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
                    "http://api.currencylayer.com/historical?access_key=$apiKey&date=$formattedDate&currencies=$currencyFrom,$currencyTo",
                )->getBody()->getContents(),
                true
            );
        }

        throw new \Exception('Please add CURRENCY_CONVERTER_ACCESS_KEY in your .env.local file');
    }
}
