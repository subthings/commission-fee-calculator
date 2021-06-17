<?php

declare(strict_types=1);

namespace CommissionTask\Model;

use CommissionTask\Service\CalculateCommission\CalculatePrivateWithdrawCommission;

class PrivateWithdrawOperation extends Operation
{
    public function __construct(array $operationRow, CalculatePrivateWithdrawCommission $calculateCommission)
    {
        parent::__construct($operationRow, $calculateCommission);
    }
}