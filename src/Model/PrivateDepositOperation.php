<?php

declare(strict_types=1);

namespace CommissionTask\Model;

use CommissionTask\Service\CalculateCommission\CalculateDepositCommission;

class PrivateDepositOperation extends Operation
{
    public function __construct(array $operationRow, CalculateDepositCommission $calculateCommission)
    {
        parent::__construct($operationRow, $calculateCommission);
    }
}
