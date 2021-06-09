<?php

declare(strict_types=1);

namespace CommissionTask\Model;

class Operation
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

    /** @var \DateTime */
    private $date;
    /** @var int */
    private $userId;
    /** @var int */
    private $userType;
    /** @var int */
    private $operationType;
    /** @var string */
    private $operationAmount;
    /** @var string */
    private $operationCurrency;

    public function __construct(array $operationRow)
    {
        $this->date = new \DateTime($operationRow[0]);
        $this->userId = (int) $operationRow[1];
        $this->userType = self::USER_TYPES[$operationRow[2]];
        $this->operationType = self::OPERATION_TYPES[$operationRow[3]];
        $this->operationAmount = $operationRow[4];
        $this->operationCurrency = $operationRow[5];
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
}
