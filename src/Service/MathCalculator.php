<?php

declare(strict_types=1);

namespace CommissionTask\Service;

use CommissionTask\Model\Operation;

class MathCalculator
{
    private UserBalanceStore $balanceStore;
    private CurrencyService $currencyService;

    public function __construct(UserBalanceStore $balanceStore, CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
        $this->balanceStore = $balanceStore;
    }

    public function computeCommission(Operation $operation): string
    {
        if ($operation->getOperationType() === Operation::OPERATION_TYPES[Operation::DEPOSIT_TYPE]) {
            return $this->computeDepositCommission($operation->getOperationAmount());
        }

        if ($operation->getUserType() === Operation::USER_TYPES[Operation::BUSINESS_CLIENT]) {
            return $this->computeBusinessWithdrawCommission($operation->getOperationAmount());
        }

        return $this->computePrivateWithdrawCommission(
            $operation->getOperationAmount(),
            $operation->getUserId(),
            $operation->getDate(),
            $operation->getOperationCurrency()
        );
    }

    public function computeDepositCommission(string $deposit): string
    {
        return bcmul($deposit, getenv('DEPOSIT_FEE'), 2);
    }

    public function computeBusinessWithdrawCommission(string $deposit): string
    {
        return bcmul($deposit, getenv('WITHDRAW_BUSINESS_FEE'), 2);
    }

    public function computePrivateWithdrawCommission(
        string $amount,
        int $userId,
        \DateTime $date,
        string $currency
    ): string {
        $euroAmount = $this->currencyService->getDefaultFromCurrencyAmount($currency, $amount, $date);
        $mondayDate = date('d-M-Y', strtotime("Monday this week {$date->format('Y-M-d')}"));
        $this->balanceStore->addAmount($userId, $mondayDate, $euroAmount);

        if ($this->balanceStore->getCount($userId, $mondayDate) > getenv('FREE_OPERATIONS')) {
            return bcmul($amount, getenv('WITHDRAW_PRIVATE_FEE'), 2);
        }

        $operationAmount = $this->balanceStore->getAmount($userId, $mondayDate);
        if (($remainingFreeAmount = bcsub($operationAmount, getenv('WITHDRAW_PRIVATE_LIMIT'), 2)) > 0) {
            return bcmul(
                (min(
                    $this->currencyService->getCurrencyFromDefaultAmount(
                        $currency,
                        $remainingFreeAmount,
                        $date
                    ),
                    $amount
                )), getenv('WITHDRAW_PRIVATE_FEE'),
                2
            );
        }

        return '0.00';
    }
}
