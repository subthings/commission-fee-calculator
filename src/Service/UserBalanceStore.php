<?php

declare(strict_types=1);

namespace CommissionTask\Service;

class UserBalanceStore
{
    public function addAmount(int $userId, string $mondayDate, string $euroAmount): void
    {
        if (isset($_COOKIE[$userId][$mondayDate])) {
            ++$_COOKIE[$userId][$mondayDate]['count'];
            $_COOKIE[$userId][$mondayDate]['amount'] = bcadd($euroAmount, $_COOKIE[$userId][$mondayDate]['amount'], 2);
        } else {
            $_COOKIE[$userId][$mondayDate]['count'] = 1;
            $_COOKIE[$userId][$mondayDate]['amount'] = $euroAmount;
        }
    }

    public function getAmount(int $userId, string $mondayDate): string
    {
        return $_COOKIE[$userId][$mondayDate]['amount'];
    }

    public function getCount(int $userId, string $mondayDate): int
    {
        return $_COOKIE[$userId][$mondayDate]['count'];
    }
}
