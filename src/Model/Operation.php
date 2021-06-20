<?php

declare(strict_types=1);

namespace CommissionTask\Model;

use CommissionTask\Service\CalculateCommission\AbstractCalculateCommission;

abstract class Operation
{
    public const PRIVATE_CLIENT = 'private';
    public const BUSINESS_CLIENT = 'business';
    public const USER_TYPES = [
        self::PRIVATE_CLIENT => 0,
        self::BUSINESS_CLIENT => 1,
    ];

    public const WITHDRAW_TYPE = 'withdraw';
    public const DEPOSIT_TYPE = 'deposit';
    public const OPERATION_TYPES = [
        self::WITHDRAW_TYPE => 0,
        self::DEPOSIT_TYPE => 1,
    ];

    private \DateTime $date;
    private int $userId;
    private int $userType;
    private int $operationType;
    private string $operationAmount;
    private string $operationCurrency;
    private AbstractCalculateCommission $calculateCommission;

    public function __construct(array $operationRow, AbstractCalculateCommission $calculateCommission)
    {
        $this->date = new \DateTime($operationRow[0]);
        $this->userId = (int) $operationRow[1];
        $this->userType = self::USER_TYPES[$operationRow[2]];
        $this->operationType = self::OPERATION_TYPES[$operationRow[3]];
        $this->operationAmount = $operationRow[4];
        $this->operationCurrency = $operationRow[5];
        $this->calculateCommission = $calculateCommission;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getUserType(): int
    {
        return $this->userType;
    }

    public function getOperationType(): int
    {
        return $this->operationType;
    }

    public function getOperationAmount(): string
    {
        return $this->operationAmount;
    }

    public function getOperationCurrency(): string
    {
        return $this->operationCurrency;
    }

    public function getNumberOfDecimal(): int
    {
        return strrchr($this->getOperationAmount(), '.') ? strlen(substr(strrchr($this->getOperationAmount(), '.'), 1)) : 0;
    }

    public function getCommission(): string
    {
        return $this->calculateCommission->getCommission($this->getOperationAmount(), $this->getUserId(), $this->getDate(), $this->getOperationCurrency(), $this->getNumberOfDecimal());
    }
}
