<?php

declare(strict_types=1);

namespace CommissionTask\Service;

use CommissionTask\Model\Operation;

class MathCalculator
{
    private const DEPOSIT_FEE = '0.0003';
    private const WITHDRAW_PRIVATE_FEE = '0.003';
    private const WITHDRAW_BUSINESS_FEE = '0.005';
    private const WITHDRAW_PRIVATE_LIMIT = '1000';
    private const FREE_OPERATIONS = '3';

    /** @var UserBalanceStore */
    private $balanceStore;
    /** @var CurrencyService */
    private $currencyService;

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
        return bcmul($deposit, self::DEPOSIT_FEE, 2);
    }

    public function computeBusinessWithdrawCommission(string $deposit): string
    {
        return bcmul($deposit, self::WITHDRAW_BUSINESS_FEE, 2);
    }

    public function computePrivateWithdrawCommission(
        string $amount,
        int $userId,
        \DateTime $date,
        string $currency
    ): string {
        $euroAmount = $this->currencyService->getEuroFromCurrencyAmount($currency, $amount, $date);
        $mondayDate = date('d-M-Y', strtotime("Monday this week {$date->format('Y-M-d')}"));
        $this->balanceStore->addAmount($userId, $mondayDate, $euroAmount);

        if ($this->balanceStore->getCount($userId, $mondayDate) > self::FREE_OPERATIONS) {
            return bcmul($amount, self::WITHDRAW_PRIVATE_FEE, 2);
        }

        $operationAmount = $this->balanceStore->getAmount($userId, $mondayDate);
        if (($remainingFreeAmount = bcsub($operationAmount, self::WITHDRAW_PRIVATE_LIMIT, 2)) > 0) {
            return bcmul(
                (min(
                    $this->currencyService->getCurrencyFromEuroAmount(
                        $currency,
                        $remainingFreeAmount,
                        $date
                    ),
                    $amount
                )), self::WITHDRAW_PRIVATE_FEE,
                2
            );
        }

        return '0';
    }
}
