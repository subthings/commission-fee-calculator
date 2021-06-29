<?php

declare(strict_types=1);

namespace CommissionTask\Factory;

use CommissionTask\Model\BusinessDepositOperation;
use CommissionTask\Model\BusinessWithdrawOperation;
use CommissionTask\Model\Operation;
use CommissionTask\Model\PrivateDepositOperation;
use CommissionTask\Model\PrivateWithdrawOperation;
use CommissionTask\Service\CalculateCommission\CalculateBusinessWithdrawCommission;
use CommissionTask\Service\CalculateCommission\CalculateDepositCommission;
use CommissionTask\Service\CalculateCommission\CalculatePrivateWithdrawCommission;
use CommissionTask\Service\CurrencyService;
use CommissionTask\Service\MoneyCalculator;
use CommissionTask\Service\UserBalanceStore;

class OperationFactory
{
    public function __construct(
        private UserBalanceStore $userBalanceStore,
        private CurrencyService $currencyService,
        private MoneyCalculator $moneyCalculator
    ) {
    }

    public function createOperationByTypes(array $row): Operation
    {
        return match ([$row[2], $row[3]]) {
            [Operation::BUSINESS_CLIENT, Operation::DEPOSIT_TYPE] => new BusinessDepositOperation($row, new CalculateDepositCommission($this->moneyCalculator)),
            [Operation::PRIVATE_CLIENT, Operation::DEPOSIT_TYPE] => new PrivateDepositOperation($row, new CalculateDepositCommission($this->moneyCalculator)),
            [Operation::BUSINESS_CLIENT, Operation::WITHDRAW_TYPE] => new BusinessWithdrawOperation($row, new CalculateBusinessWithdrawCommission($this->moneyCalculator)),
            [Operation::PRIVATE_CLIENT, Operation::WITHDRAW_TYPE] => new PrivateWithdrawOperation($row, new CalculatePrivateWithdrawCommission(
                    $this->userBalanceStore,
                    $this->currencyService,
                    $this->moneyCalculator
                )
            ),
            default => throw new \Error("Operation type '$row[2] $row[3]' is not supported"),
        };
    }
}
