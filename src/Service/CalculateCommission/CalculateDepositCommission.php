<?php

declare(strict_types=1);

namespace CommissionTask\Service\CalculateCommission;

class CalculateDepositCommission extends CalculateCommissionInterface
{
    public function getCommissionFeePercent(): string
    {
        return getenv('DEPOSIT_FEE');
    }

    public function getCommissionChargedValue(string $amount, int $userId, \DateTime $date, string $currency): string
    {
        return $amount;
    }
}
