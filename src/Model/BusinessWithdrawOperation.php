<?php

declare(strict_types=1);

namespace CommissionTask\Model;

use CommissionTask\Service\CalculateCommission\CalculateBusinessWithdrawCommission;

class BusinessWithdrawOperation extends Operation
{
    public function __construct(array $operationRow, CalculateBusinessWithdrawCommission $calculateCommission)
    {
        parent::__construct($operationRow, $calculateCommission);
    }
}
