<?php

declare(strict_types=1);

namespace CommissionTask\Service;

use GuzzleHttp\Client;

class CurrencyService
{
    private Client $client;
    private MoneyCalculator $moneyCalculator;
    private string $defaultCurrency;
    private array $store = [];

    public function __construct(Client $client, MoneyCalculator $moneyCalculator, string $defaultCurrency)
    {
        $this->client = $client;
        $this->moneyCalculator = $moneyCalculator;
        $this->defaultCurrency = $defaultCurrency;
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
        if (!isset($this->store[$formattedDate][$currencyFrom], $this->store[$formattedDate][$currencyTo])) {
            $responseContent = $this->requestCurrencies($formattedDate);

            if ($responseContent['success']) {
                $this->store[$formattedDate][$currencyTo] = $responseContent['quotes']["USD$currencyTo"];
                $this->store[$formattedDate][$currencyFrom] = $responseContent['quotes']["USD$currencyFrom"];
            } elseif ($responseContent['error'] && $responseContent['error']['info']) {
                throw new \Exception("Problem with api: {$responseContent['error']['info']}");
            } else {
                throw new \Exception('Unexpected problem with api.');
            }
        }

        return $this->moneyCalculator->roundUpMul(
            $this->moneyCalculator->roundUpDiv(
                $amount,
                (string) $this->store[$formattedDate][$currencyFrom],
                $currencyTo
            ),
            (string) $this->store[$formattedDate][$currencyTo],
            $currencyTo
        );
    }

    public function getCurrencyFromDefaultAmount(string $currencyTo, string $amount, \DateTime $date): string
    {
        return $this->getConvertedAmount($this->defaultCurrency, $currencyTo, $amount, $date);
    }

    public function getDefaultFromCurrencyAmount(string $currencyFrom, string $amount, \DateTime $date): string
    {
        return $this->getConvertedAmount($currencyFrom, $this->defaultCurrency, $amount, $date);
    }

    public function requestCurrencies(string $formattedDate): array
    {
        if ($apiKey = getenv('CURRENCY_CONVERTER_ACCESS_KEY')) {
            // had to add query this way didn't work in array
            return json_decode(
                $this->client->get(
                    getenv(
                        'CURRENCY_CONVERTER_API'
                    )."/historical?access_key=$apiKey&date=$formattedDate",
                )->getBody()->getContents(),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        }

        throw new \Exception('Please add CURRENCY_CONVERTER_ACCESS_KEY in your .env.local file');
    }
}
