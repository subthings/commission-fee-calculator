<?php

declare(strict_types=1);

namespace CommissionTask\Factory;

use CommissionTask\Model\Operation;
use CommissionTask\Service\CalculateCommission\CalculateBusinessWithdrawCommission;
use CommissionTask\Service\CalculateCommission\CalculateDepositCommission;
use CommissionTask\Service\CalculateCommission\CalculatePrivateWithdrawCommission;
use CommissionTask\Service\CurrencyService;
use CommissionTask\Service\MoneyCalculator;
use CommissionTask\Service\UserBalanceStore;

class OperationFactory
{
    public static function createOperationByTypes(
        array $row,
        UserBalanceStore $userBalanceStore,
        CurrencyService $currencyService,
        MoneyCalculator $moneyCalculator
    ): ?Operation {
        if ($row[3] === Operation::DEPOSIT_TYPE) {
            return new Operation($row, new CalculateDepositCommission($moneyCalculator));
        }

        if ($row[2] === Operation::BUSINESS_CLIENT) {
            return new Operation($row, new CalculateBusinessWithdrawCommission($moneyCalculator));
        }

        if ($row[2] === Operation::PRIVATE_CLIENT) {
            return new Operation(
                $row,
                new CalculatePrivateWithdrawCommission($userBalanceStore, $currencyService, $moneyCalculator)
            );
        }

        return null;
    }
}
