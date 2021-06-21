<?php

declare(strict_types=1);

namespace CommissionTask\Service;

class MoneyCalculator
{
    public function roundUpMul(string $multiplier1, string $multiplier2, int $scale): string
    {
        return $this->roundUpMoney(bcmul($multiplier1, $multiplier2, $scale * 2 + 6), $scale);
    }

    public function roundUpDiv(string $dividend, string $divider, $scale): string
    {
        return $this->roundUpMoney(bcdiv($dividend, $divider, 10), $scale);
    }

    public function sub($minuend, $subtrahend): string
    {
        return bcsub($minuend, $subtrahend, 2);
    }

    public function add($addend1, $addend2): string
    {
        return bcadd($addend1, $addend2, 2);
    }

    public function roundUpMoney(string $value, int $scale): string
    {
        $round = pow(10, $scale);

        return bcdiv((string) ceil($value * $round), (string) $round, $scale);
    }
}
