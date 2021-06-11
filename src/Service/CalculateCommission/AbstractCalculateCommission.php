<?php

declare(strict_types=1);

namespace CommissionTask\Service\CalculateCommission;

use CommissionTask\Service\MoneyCalculator;

abstract class AbstractCalculateCommission
{
    final public function getCommission(
        string $amount,
        int $userId,
        \DateTime $date,
        string $currency
    ): string {
        return MoneyCalculator::roundUpMul(
            $this->getCommissionChargedValue($amount, $userId, $date, $currency),
            $this->getCommissionFeePercent()
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
