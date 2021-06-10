<?php

declare(strict_types=1);

namespace CommissionTask\Service\CalculateCommission;

abstract class AbstractCalculateCommission
{
    final public function getCommission(
        string $amount,
        int $userId,
        \DateTime $date,
        string $currency
    ): string {
        return bcmul(
            $this->getCommissionChargedValue($amount, $userId, $date, $currency),
            $this->getCommissionFeePercent(),
            2
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
