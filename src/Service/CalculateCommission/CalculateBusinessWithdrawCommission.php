<?php

declare(strict_types=1);

namespace CommissionTask\Service\CalculateCommission;

class CalculateBusinessWithdrawCommission extends AbstractCalculateCommission
{
    public function getCommissionFeePercent(): string
    {
        return getenv('WITHDRAW_BUSINESS_FEE');
    }

    public function getCommissionChargedValue(string $amount, int $userId, \DateTime $date, string $currency, int $scale): string
    {
        return $amount;
    }
}
