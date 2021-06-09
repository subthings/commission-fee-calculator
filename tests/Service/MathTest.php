<?php

declare(strict_types=1);

namespace CommissionTask\Tests\Service;

use CommissionTask\Model\Operation;
use CommissionTask\Service\CurrencyService;
use CommissionTask\Service\UserBalanceStore;
use PHPUnit\Framework\TestCase;
use CommissionTask\Service\MathCalculator;

class MathTest extends TestCase
{
    /** @var MathCalculator $math */
    private $math;

    public function setUp()
    {
        $userBalanceStore = new UserBalanceStore();
        $mockResponse = $this->getMockBuilder(CurrencyService::class)
            ->disableOriginalConstructor()->setMethods(
                ['requestCurrencies']
            )
            ->getMock();
        $mockResponse->expects($this->any())
            ->method('requestCurrencies')->willReturn(
                [
                    'quotes' => ['USDJPY' => 129.53 / 1.1497, 'USDEUR' => 1 / 1.1497, 'USDUSD' => 1],
                    'success' => true,
                ]
            );
        $this->math = new MathCalculator($userBalanceStore, $mockResponse);
    }

    /**
     * @dataProvider dataProviderForComputeCommissionTest
     * @param string $date
     * @param string $userId
     * @param string $userType
     * @param string $operationType
     * @param string $amount
     * @param string $currency
     * @param string $expectation
     */
    public function testComputeCommission(
        string $date,
        string $userId,
        string $userType,
        string $operationType,
        string $amount,
        string $currency,
        string $expectation
    ) {
        $this->assertEquals(
            $expectation,
            $this->math->computeCommission(
                new Operation([$date, $userId, $userType, $operationType, $amount, $currency])
            )
        );
    }

    public function dataProviderForComputeCommissionTest(): array
    {
        return [
            'compute fee for 1200.00 EUR private withdraw at 2014-12-31' => [
                '2014-12-31',
                4,
                'private',
                'withdraw',
                1200.00,
                'EUR',
                0.60
            ],
            'compute fee for 1000.00 EUR private withdraw at 2015-01-01' => [
                '2015-01-01',
                4,
                'private',
                'withdraw',
                1000.00,
                'EUR',
                3.00
            ],
            'compute fee for 1000.00 EUR private withdraw at 2016-01-05' => [
                '2016-01-05',
                4,
                'private',
                'withdraw',
                1000.00,
                'EUR',
                0.00
            ],
            'compute fee for 200.00 EUR private deposit at 2016-01-05' => [
                '2016-01-05',
                1,
                'private',
                'deposit',
                200,
                'EUR',
                0.06
            ],
            'compute fee for 200.00 EUR business withdraw 2016-01-06' => [
                '2016-01-06',
                2,
                'business',
                'withdraw',
                300.00,
                'EUR',
                1.50
            ],
            'compute fee for 30000 JPY private withdraw at 2016-01-06' => [
                '2016-01-06',
                1,
                'private',
                'withdraw',
                30000,
                'JPY',
                0
            ],
            'compute fee for 1000.00 EUR private withdraw at 2016-01-07' => [
                '2016-01-07',
                1,
                'private',
                'withdraw',
                1000.00,
                'EUR',
                0.70
            ],
            'compute fee for 100.00 USD private withdraw at 2016-01-07' => [
                '2016-01-07',
                1,
                'private',
                'withdraw',
                100.00,
                'USD',
                0.30
            ],
            'compute fee for 100.00 EUR private withdraw at 2016-01-10' => [
                '2016-01-10',
                1,
                'private',
                'withdraw',
                100.00,
                'EUR',
                0.30
            ],
            'compute fee for 10000.00 EUR private deposit at 2016-01-10' => [
                '2016-01-10',
                2,
                'business',
                'deposit',
                10000.00,
                'EUR',
                3.00
            ],
            'compute fee for 1000.00 EUR private withdraw at 2016-01-10' => [
                '2016-01-10',
                3,
                'private',
                'withdraw',
                1000.00,
                'EUR',
                0.00
            ],
            'compute fee for 300.00 EUR private withdraw at 2016-02-15' => [
                '2016-02-15',
                1,
                'private',
                'withdraw',
                300.00,
                'EUR',
                0.00
            ],
            'compute fee for 3000000 JPY private withdraw at 2016-02-19' => [
                '2016-02-19',
                5,
                'private',
                'withdraw',
                3000000,
                'JPY',
                8612
            ],
        ];
    }
}
