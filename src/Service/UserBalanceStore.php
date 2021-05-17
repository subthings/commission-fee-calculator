<?php

declare(strict_types=1);

namespace CommissionTask\Service;

class UserBalanceStore
{
    public function addAmount(int $userId, string $mondayDate, float $euroAmount): void
    {
        if (isset($_COOKIE[$userId][$mondayDate])) {
            ++$_COOKIE[$userId][$mondayDate]['count'];
            $_COOKIE[$userId][$mondayDate]['amount'] += $euroAmount;
        } else {
            $_COOKIE[$userId][$mondayDate]['count'] = 1;
            $_COOKIE[$userId][$mondayDate]['amount'] = $euroAmount;
        }
    }

    public function getAmount(int $userId, string $mondayDate): float
    {
        return $_COOKIE[$userId][$mondayDate]['amount'];
    }

    public function getCount(int $userId, string $mondayDate)
    {
        return $_COOKIE[$userId][$mondayDate]['count'];
    }
}
