<?php

declare(strict_types=1);

namespace CommissionTask\Tests\Service;

use CommissionTask\Model\Operation;
use CommissionTask\Service\CalculateCommission\CalculateBusinessWithdrawCommission;
use CommissionTask\Service\CalculateCommission\CalculateDepositCommission;
use CommissionTask\Service\CalculateCommission\CalculatePrivateWithdrawCommission;
use CommissionTask\Service\CurrencyService;
use CommissionTask\Service\MoneyCalculator;
use CommissionTask\Service\UserBalanceStore;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class CalculateCommissionTest extends TestCase
{
    private UserBalanceStore $userBalanceStore;
    private MoneyCalculator $moneyCalculator;

    public function setUp(): void
    {
        parent::setUp();
        $this->moneyCalculator = new MoneyCalculator();
        $this->userBalanceStore = new UserBalanceStore($this->moneyCalculator);
    }

    /**
     * @dataProvider dataProviderForComputeCommissionTest
     *
     * @param string $date
     * @param string $userId
     * @param string $userType
     * @param string $operationType
     * @param string $amount
     * @param string $currency
     * @param array $userBalance
     * @param string $expectation
     */
    public function testComputeCommission(
        string $date,
        string $userId,
        string $userType,
        string $operationType,
        string $amount,
        string $currency,
        array $userBalance,
        string $expectation
    ) {
        $this->assertEquals(
            $expectation,
            ($this->createOperationByTypes(
                [
                    $date,
                    $userId,
                    $userType,
                    $operationType,
                    $amount,
                    $currency,
                    $userBalance,
                ]
            )->getCommission()
            )
        );
    }

    public function dataProviderForComputeCommissionTest(): array
    {
        return [
            'compute fee for 1200.00 EUR private withdraw at 2014-12-31' => [
                '2014-12-31',
                '4',
                'private',
                'withdraw',
                '1200.00',
                'EUR',
                ['0.00'],
                '0.60',
            ],
            'compute fee for 1000.00 EUR private withdraw at 2015-01-01' => [
                '2015-01-01',
                '4',
                'private',
                'withdraw',
                '1000.00',
                'EUR',
                ['1200.00'],
                '3.00',
            ],
            'compute fee for 1000.00 EUR private withdraw at 2016-01-05' => [
                '2016-01-05',
                '4',
                'private',
                'withdraw',
                '1000.00',
                'EUR',
                ['0.00'],
                '0.00',
            ],
            'compute fee for 200.00 EUR private deposit at 2016-01-05' => [
                '2016-01-05',
                '1',
                'private',
                'deposit',
                '200',
                'EUR',
                ['0.00'],
                '0.06',
            ],
            'compute fee for 200.00 EUR business withdraw 2016-01-06' => [
                '2016-01-06',
                '2',
                'business',
                'withdraw',
                '300.00',
                'EUR',
                ['1000.00'],
                '1.50',
            ],
            'compute fee for 30000 JPY private withdraw at 2016-01-06' => [
                '2016-01-06',
                '1',
                'private',
                'withdraw',
                '30000',
                'JPY',
                ['0.00'],
                '0.00',
            ],
            'compute fee for 1000.00 EUR private withdraw at 2016-01-07' => [
                '2016-01-07',
                '1',
                'private',
                'withdraw',
                '1000.00',
                'EUR',
                ['231.61'],
                '0.70',
            ],
            'compute fee for 100.00 USD private withdraw at 2016-01-07' => [
                '2016-01-07',
                '1',
                'private',
                'withdraw',
                '100.00',
                'USD',
                ['231.61', '1000.00'],
                '0.30',
            ],
            'compute fee for 100.00 EUR private withdraw at 2016-01-10' => [
                '2016-01-10',
                '1',
                'private',
                'withdraw',
                '100.00',
                'EUR',
                ['231.61', '1000.00', '86.9'],
                '0.30',
            ],
            'compute fee for 10000.00 EUR private deposit at 2016-01-10' => [
                '2016-01-10',
                '2',
                'business',
                'deposit',
                '10000.00',
                'EUR',
                ['0.00'],
                '3.00',
            ],
            'compute fee for 1000.00 EUR private withdraw at 2016-01-10' => [
                '2016-01-10',
                '3',
                'private',
                'withdraw',
                '1000.00',
                'EUR',
                ['0.00'],
                '0.00',
            ],
            'compute fee for 300.00 EUR private withdraw at 2016-02-15' => [
                '2016-02-15',
                '1',
                'private',
                'withdraw',
                '300.00',
                'EUR',
                ['0.00'],
                '0.00',
            ],
            'compute fee for 3000000 JPY private withdraw at 2016-02-19' => [
                '2016-02-19',
                '5',
                'private',
                'withdraw',
                '3000000',
                'JPY',
                ['0.00'],
                '8611.42',
            ],
        ];
    }

    public function createOperationByTypes(array $row): ?Operation
    {
        if ($row[3] === Operation::DEPOSIT_TYPE) {
            return new Operation(array_slice($row, 0, 6), new CalculateDepositCommission($this->moneyCalculator));
        }

        if ($row[2] === Operation::BUSINESS_CLIENT) {
            return new Operation(
                array_slice($row, 0, 6),
                new CalculateBusinessWithdrawCommission($this->moneyCalculator)
            );
        }

        if ($row[2] === Operation::PRIVATE_CLIENT) {
            $mockCurrencyService = $this->getMockBuilder(CurrencyService::class)
                ->setConstructorArgs(
                    [
                        $this->getMockBuilder(Client::class)->getMock(),
                        $this->moneyCalculator,
                        getenv('DEFAULT_CURRENCY')
                    ]
                )->onlyMethods(
                    ['requestCurrencies']
                )
                ->getMock();
            $mockCurrencyService->expects($this->any())
                ->method('requestCurrencies')->willReturn(
                    [
                        'quotes' => ['USDJPY' => 129.53 / 1.1497, 'USDEUR' => 1 / 1.1497, 'USDUSD' => 1],
                        'success' => true,
                    ]
                );

            foreach($row[6] as $previousOperation) {
                $this->userBalanceStore->addAmount(
                    (int)$row[1],
                    date('d-M-Y', strtotime("Monday this week $row[0]")),
                    $previousOperation
                );
            }

            return new Operation(
                array_slice($row, 0, 6),
                new CalculatePrivateWithdrawCommission(
                    $this->userBalanceStore,
                    $mockCurrencyService,
                    $this->moneyCalculator
                )
            );
        }

        return null;
    }
}
