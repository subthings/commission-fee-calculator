<?php

declare(strict_types=1);

namespace CommissionTask\Tests\Service;

use CommissionTask\Service\UserBalanceStore;
use PHPUnit\Framework\TestCase;
use CommissionTask\Service\MathCalculator;

class MathTest extends TestCase
{
    private MathCalculator $math;

    public function setUp()
    {
        $userBalanceStore = new UserBalanceStore();
        $this->math = new MathCalculator($userBalanceStore);
    }

    /**
     * @param float $deposit
     * @param float $expectation
     *
     * @dataProvider dataProviderForComputeDepositCommissionTest
     */
    public function testComputeDepositCommission(float $deposit, float $expectation)
    {
        $this->assertEquals($expectation, $this->math->computeDepositCommission($deposit));
    }

    public function dataProviderForComputeDepositCommissionTest(): array
    {
        return [
            'compute fee for 200.00 deposit' => [200, 0.06],
            'compute fee for 201.00 deposit' => [201, 0.07],
            'compute fee for 220.00 deposit' => [220, 0.07],
            'compute fee for -2 deposit' => [-2, 0],
        ];
    }

    /**
     * @param float $deposit
     * @param int $userId
     * @param \DateTime $date
     * @param string $currency
     * @param float $expectation
     *
     * @dataProvider dataPrivateWithdrawCommissionTest
     */
    public function testComputePrivateWithdrawCommission(\DateTime $date, int $userId, float $deposit, string $currency, float $expectation)
    {
        $this->assertEquals($expectation, $this->math->computePrivateWithdrawCommission($deposit, $userId, $date, $currency));
    }

    public function dataPrivateWithdrawCommissionTest(): array
    {
        return [
            'compute fee for 1200.00 EUR private withdraw at 2014-12-31' => [new \DateTime('2014-12-31'), 4, 1200.00, 'EUR', 0.60],
            'compute fee for 1000.00 EUR private withdraw at 2015-01-01' => [new \DateTime('2015-01-01'), 4, 1000.00, 'EUR', 3.00],
            'compute fee for 1000.00 EUR private withdraw at 2016-01-05' => [new \DateTime('2016-01-05'), 4, 1000.00, 'EUR', 0.00],
            'compute fee for 30000 JPY private withdraw at 2016-01-06'   => [new \DateTime('2016-01-06'), 1, 30000, 'JPY', 0],
            'compute fee for 1000.00 EUR private withdraw at 2016-01-07' => [new \DateTime('2016-01-07'), 1, 1000.00, 'EUR', 0.70],
            'compute fee for 100.00 USD private withdraw at 2016-01-07'  => [new \DateTime('2016-01-07'), 1, 100.00, 'USD', 0.30],
            'compute fee for 100.00 EUR private withdraw at 2016-01-10'  => [new \DateTime('2016-01-10'), 1, 100.00, 'EUR', 0.30],
            'compute fee for 1000.00 EUR private withdraw at 2016-01-10' => [new \DateTime('2016-01-10'), 3, 1000.00, 'EUR', 0.00],
            'compute fee for 300.00 EUR private withdraw at 2016-02-15'  => [new \DateTime('2016-02-15'), 1, 300.00, 'EUR', 0.00],
            'compute fee for 3000000 JPY private withdraw at 2016-02-19' => [new \DateTime('2016-02-19'), 5, 3000000, 'JPY', 8612],
        ];
    }

    /**
     * @param float $deposit
     * @param float $expectation
     *
     * @dataProvider dataBusinessWithdrawCommissionTest
     */
    public function testComputeBusinessWithdrawCommission(float $deposit, float $expectation)
    {
        $this->assertEquals($expectation, $this->math->computeBusinessWithdrawCommission($deposit));
    }

    public function dataBusinessWithdrawCommissionTest(): array
    {
        return [
            'compute fee for 200.00 business withdraw' => [300.00, 1.50],
        ];
    }
}
