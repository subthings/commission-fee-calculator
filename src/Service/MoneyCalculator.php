<?php

declare(strict_types=1);

namespace CommissionTask\Service;

use Symfony\Component\Intl\Currencies;

class MoneyCalculator
{
    public function roundUpMul(string $multiplier1, string $multiplier2, string $currency): string
    {
        return $this->roundUpMoney(bcmul($multiplier1, $multiplier2, (int) getenv('MAX_CURRENCY_DECIMAL') * 2), $currency);
    }

    public function roundUpDiv(string $dividend, string $divider, string $currency): string
    {
        return $this->roundUpMoney(bcdiv($dividend, $divider, (int) getenv('MAX_SCALE')), $currency);
    }

    public function sub($minuend, $subtrahend): string
    {
        return bcsub($minuend, $subtrahend, (int) getenv('MAX_CURRENCY_DECIMAL'));
    }

    public function add($addend1, $addend2): string
    {
        return bcadd($addend1, $addend2, (int) getenv('MAX_CURRENCY_DECIMAL'));
    }

    public function roundUpMoney(string $value, string $currency): string
    {
        $round = pow(10, Currencies::getFractionDigits($currency));

        return bcdiv((string) ceil($value * $round), (string) $round, Currencies::getFractionDigits($currency));
    }
}
