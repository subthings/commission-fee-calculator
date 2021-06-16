<?php

declare(strict_types=1);

namespace CommissionTask\Tests\Service;

use CommissionTask\Service\MoneyCalculator;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    private MoneyCalculator $moneyCalculator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->moneyCalculator = new MoneyCalculator();
    }

    /**
     * @dataProvider dataProviderForRoundUpCommissionTest
     *
     * @param string $number
     * @param string $expectation
     */
    public function testRoundUpCommission(string $number, string $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->moneyCalculator->roundUpMoney($number)
        );
    }

    public function dataProviderForRoundUpCommissionTest(): array
    {
        return [
            'round up 0.023' => ['0.023', '0.03'],
            'round up 0.0203' => ['0.0203', '0.03'],
            'round up 0.05003' => ['0.05003', '0.06'],
            'round up 0.0003' => ['0.0003', '0.01'],
            'round up 0.6000' => ['0.6000', '0.60'],
            'round up 6.6947' => ['6.6947', '6.70'],
        ];
    }
}