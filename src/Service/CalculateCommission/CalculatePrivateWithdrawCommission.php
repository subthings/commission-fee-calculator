<?php

declare(strict_types=1);

namespace CommissionTask\Service\CalculateCommission;

use CommissionTask\Service\CurrencyService;
use CommissionTask\Service\UserBalanceStore;

class CalculatePrivateWithdrawCommission extends CalculateCommissionInterface
{
    private UserBalanceStore $balanceStore;
    private CurrencyService $currencyService;

    public function __construct(UserBalanceStore $balanceStore, CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
        $this->balanceStore = $balanceStore;
    }

    public function getCommissionFeePercent(): string
    {
        return getenv('WITHDRAW_PRIVATE_FEE');
    }

    public function getCommissionChargedValue(string $amount, int $userId, \DateTime $date, string $currency): string
    {
        $euroAmount = $this->currencyService->getDefaultFromCurrencyAmount($currency, $amount, $date);
        $mondayDate = date('d-M-Y', strtotime("Monday this week {$date->format('Y-M-d')}"));
        $this->balanceStore->addAmount($userId, $mondayDate, $euroAmount);

        if ($this->balanceStore->getCount($userId, $mondayDate) > getenv('WITHDRAW_PRIVATE_FREE_OPERATIONS')) {
            return $amount;
        }

        $operationAmount = $this->balanceStore->getAmount($userId, $mondayDate);
        if (($remainingFreeAmount = bcsub($operationAmount, getenv('WITHDRAW_PRIVATE_LIMIT'), 2)) > 0) {
            return min(
                $this->currencyService->getCurrencyFromDefaultAmount(
                    $currency,
                    $remainingFreeAmount,
                    $date
                ),
                $amount
            );
        }

        return '0.00';
    }
}
