<?php

declare(strict_types=1);

namespace CommissionTask\Service\CalculateCommission;

use CommissionTask\Service\MoneyCalculator;

abstract class AbstractCalculateCommission
{
    private MoneyCalculator $moneyCalculator;

    public function __construct(MoneyCalculator $moneyCalculator)
    {
        $this->moneyCalculator = $moneyCalculator;
    }

    final public function getCommission(
        string $amount,
        int $userId,
        \DateTime $date,
        string $currency
    ): string {
        return $this->moneyCalculator->roundUpMul(
            $this->getCommissionChargedValue($amount, $userId, $date, $currency),
            $this->getCommissionFeePercent(),
            $currency
        );
    }

    abstract public function getCommissionFeePercent(): string;

    abstract public function getCommissionChargedValue(
        string $amount,
        int $userId,
        \DateTime $date,
        string $currency
    ): string;
}
