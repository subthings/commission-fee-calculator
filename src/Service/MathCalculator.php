<?php

declare(strict_types=1);

namespace CommissionTask\Service;

use CommissionTask\Model\Operation;

class MathCalculator
{
    const DEPOSIT_FEE = 0.0003;
    const WITHDRAW_PRIVATE_FEE = 0.003;
    const WITHDRAW_BUSINESS_FEE = 0.005;
    const WITHDRAW_PRIVATE_LIMIT = 1000;
    const FREE_OPERATIONS = 3;

    private UserBalanceStore $balanceStore;
    private CurrencyService $currencyService;

    public function __construct(UserBalanceStore $balanceStore, CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
        $this->balanceStore = $balanceStore;
    }

    public function computeCommission(Operation $operation): float
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

    public function computeDepositCommission(float $deposit): float
    {
        return $this->round_up($deposit * self::DEPOSIT_FEE);
    }

    public function computeBusinessWithdrawCommission(float $deposit): float
    {
        return $this->round_up($deposit * self::WITHDRAW_BUSINESS_FEE);
    }

    public function computePrivateWithdrawCommission(
        float $amount,
        int $userId,
        \DateTime $date,
        string $currency
    ): float {
        $euroAmount = $this->currencyService->getEuroFromCurrencyAmount($currency, $amount, $date);
        $mondayDate = date('d-M-Y', strtotime("Monday this week {$date->format('Y-M-d')}"));
        $this->balanceStore->addAmount($userId, $mondayDate, $euroAmount);

        if ($this->balanceStore->getCount($userId, $mondayDate) > self::FREE_OPERATIONS) {
            return $this->round_up($amount * self::WITHDRAW_PRIVATE_FEE);
        }

        $operationAmount = $this->balanceStore->getAmount($userId, $mondayDate);
        if ($operationAmount > self::WITHDRAW_PRIVATE_LIMIT) {
            return $this->round_up(
                (min(
                    $this->currencyService->getCurrencyFromEuroAmount(
                        $currency,
                        $operationAmount - self::WITHDRAW_PRIVATE_LIMIT,
                        $date
                    ),
                    $amount
                )) * self::WITHDRAW_PRIVATE_FEE
            );
        }

        return 0;
    }

    function round_up($number, $precision = 2)
    {
        $fig = pow(10, $precision);
        return (ceil($number * $fig) / $fig);
    }
}
