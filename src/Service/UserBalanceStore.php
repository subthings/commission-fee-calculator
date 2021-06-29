<?php

declare(strict_types=1);

namespace CommissionTask\Service;

class UserBalanceStore
{
    private array $store = [];

    public function __construct(private MoneyCalculator $moneyCalculator)
    {
    }

    public function addAmount(int $userId, string $mondayDate, string $euroAmount): void
    {
        if (isset($this->store[$userId][$mondayDate])) {
            ++$this->store[$userId][$mondayDate]['count'];
            $this->store[$userId][$mondayDate]['amount'] = $this->moneyCalculator->add(
                $euroAmount,
                $this->store[$userId][$mondayDate]['amount']
            );
        } else {
            $this->store[$userId][$mondayDate]['count'] = 1;
            $this->store[$userId][$mondayDate]['amount'] = $euroAmount;
        }
    }

    public function getAmount(int $userId, string $mondayDate): ?string
    {
        return $this->store[$userId][$mondayDate]['amount'];
    }

    public function getCount(int $userId, string $mondayDate): ?int
    {
        return $this->store[$userId][$mondayDate]['count'];
    }
}
