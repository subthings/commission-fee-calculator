<?php

declare(strict_types=1);

namespace CommissionTask\Service;

class UserBalanceStore
{
    private static array $instances = [];
    private array $store = [];
    protected function __construct() { }
    protected function __clone() { }
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    public function addAmount(int $userId, string $mondayDate, string $euroAmount): void
    {
        if (isset($this->store[$userId][$mondayDate])) {
            ++$this->store[$userId][$mondayDate]['count'];
            $this->store[$userId][$mondayDate]['amount'] = bcadd($euroAmount, $this->store[$userId][$mondayDate]['amount'], 2);
        } else {
            $this->store[$userId][$mondayDate]['count'] = 1;
            $this->store[$userId][$mondayDate]['amount'] = $euroAmount;
        }
    }

    public function getAmount(int $userId, string $mondayDate): string
    {
        return $this->store[$userId][$mondayDate]['amount'];
    }

    public function getCount(int $userId, string $mondayDate): int
    {
        return $this->store[$userId][$mondayDate]['count'];
    }

    public static function getInstance(): UserBalanceStore
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static();
        }

        return self::$instances[$cls];
    }
}
