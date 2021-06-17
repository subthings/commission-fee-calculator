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
    private UserBalanceStore $userBalanceStore;
    private CurrencyService $currencyService;
    private MoneyCalculator $moneyCalculator;

    public function __construct(
        UserBalanceStore $userBalanceStore,
        CurrencyService $currencyService,
        MoneyCalculator $moneyCalculator)
    {
        $this->userBalanceStore = $userBalanceStore;
        $this->currencyService = $currencyService;
        $this->moneyCalculator = $moneyCalculator;
    }

    public function createOperationByTypes(
        array $row
    ): Operation {
        if ($row[3] === Operation::DEPOSIT_TYPE) {
            if ($row[2] === Operation::BUSINESS_CLIENT) {
                return new BusinessDepositOperation($row, new CalculateDepositCommission($this->moneyCalculator));
            } elseif ($row[2] === Operation::PRIVATE_CLIENT) {
                return new PrivateDepositOperation($row, new CalculateDepositCommission($this->moneyCalculator));
            }
        }

        if ($row[3] === Operation::WITHDRAW_TYPE) {
            if ($row[2] === Operation::BUSINESS_CLIENT) {
                return new BusinessWithdrawOperation($row, new CalculateBusinessWithdrawCommission($this->moneyCalculator));
            } elseif ($row[2] === Operation::PRIVATE_CLIENT) {
                return new PrivateWithdrawOperation(
                    $row,
                    new CalculatePrivateWithdrawCommission($this->userBalanceStore, $this->currencyService, $this->moneyCalculator)
                );
            }
        }

        throw new \Error("Operation type '$row[2] $row[3]' is not supported");
    }
}
