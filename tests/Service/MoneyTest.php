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
     * @param string $currency
     * @param string $expectation
     */
    public function testRoundUpCommission(string $number, string $currency, string $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->moneyCalculator->roundUpMoney($number, $currency)
        );
    }

    public function dataProviderForRoundUpCommissionTest(): array
    {
        return [
            'round up 0.023 EUR' => ['0.023', 'EUR', '0.03'],
            'round up 0.0203 EUR' => ['0.0203', 'EUR', '0.03'],
            'round up 0.05003 EUR' => ['0.05003', 'EUR', '0.06'],
            'round up 0.0003 EUR' => ['0.0003', 'EUR', '0.01'],
            'round up 0.6000 EUR' => ['0.6000', 'EUR', '0.60'],
            'round up 6.6947 EUR' => ['6.6947', 'EUR', '6.70'],
            'round up 0.023 JPY' => ['0.023', 'JPY', '1'],
            'round up 0.0203 JPY' => ['0.0203', 'JPY', '1'],
            'round up 0.05003 JPY' => ['0.05003', 'JPY', '1'],
            'round up 0.0003 JPY' => ['0.0003', 'JPY', '1'],
            'round up 0.6000 JPY' => ['0.6000', 'JPY', '1'],
            'round up 6.6947 JPY' => ['6.6947', 'JPY', '7'],
        ];
    }

    /**
     * @dataProvider dataProviderForRoundUpMulTest
     *
     * @param string $mul1
     * @param string $mul2
     * @param string $currency
     * @param string $expectation
     */
    public function testRoundUpMul(string $mul1, string $mul2, string $currency, string $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->moneyCalculator->roundUpMul($mul1, $mul2, $currency)
        );
    }

    public function dataProviderForRoundUpMulTest(): array
    {
        return [
            'round up 0.01 * 0.01 JPY' => ['0.01', '0.01', 'JPY', '1'],
            'round up 0.10 * 0.10 JPY' => ['0.10', '0.10', 'JPY', '1'],
            'round up 0.36 * 0.55 JPY' => ['0.36', '0.55', 'JPY', '1'],
            'round up 0.23 * 0.32 JPY' => ['0.23', '0.32', 'JPY', '1'],
            'round up 0.01 * 0.01 EUR' => ['0.01', '0.01', 'EUR', '0.01'],
            'round up 0.10 * 0.10 EUR' => ['0.10', '0.10', 'EUR', '0.01'],
            'round up 0.36 * 0.55 EUR' => ['0.36', '0.55', 'EUR', '0.20'],
            'round up 0.23 * 0.32 EUR' => ['0.23', '0.32', 'EUR', '0.08'],
            'round up 0.01 * 0.01 BHD' => ['0.01', '0.01', 'BHD', '0.001'],
            'round up 0.10 * 0.10 BHD' => ['0.10', '0.10', 'BHD', '0.010'],
            'round up 0.36 * 0.55 BHD' => ['0.36', '0.55', 'BHD', '0.198'],
            'round up 0.23 * 0.32 BHD' => ['0.23', '0.32', 'BHD', '0.074'],
        ];
    }

    /**
     * @dataProvider dataProviderForRoundUpDivTest
     *
     * @param string $div1
     * @param string $div2
     * @param string $expectation
     */
    public function testRoundUpDiv(string $div1, string $div2, string $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->moneyCalculator->roundDiv($div1, $div2)
        );
    }

    public function dataProviderForRoundUpDivTest(): array
    {
        return [
            'round up 0.01 / 0.01 JPY' => ['0.01', '0.01', '1.0000000000'],
            'round up 0.01 / 0.03 EUR' => ['0.01', '0.03', '0.3333333333'],
            'round up 100.00 / 8.00 USD' => ['100.01', '8.00', '12.5012500000'],
        ];
    }

    /**
     * @dataProvider dataProviderForRoundAddTest
     *
     * @param string $add1
     * @param string $add2
     * @param string $expectation
     */
    public function testRoundAdd(string $add1, string $add2, string $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->moneyCalculator->add($add1, $add2)
        );
    }

    public function dataProviderForRoundAddTest(): array
    {
        return [
            'round up 0.010 + 0.03' => ['0.010', '0.03', '0.040'],
            'round up 0.011 + 0.01' => ['0.011', '0.01', '0.021'],
            'round up 0.001 + 0.001' => ['0.001', '0.00', '0.001'],
        ];
    }

    /**
     * @dataProvider dataProviderForRoundSubTest
     *
     * @param string $sub1
     * @param string $sub2
     * @param string $expectation
     */
    public function testRoundASub(string $sub1, string $sub2, string $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->moneyCalculator->sub($sub1, $sub2)
        );
    }

    public function dataProviderForRoundSubTest(): array
    {
        return [
            'round up 0.031 - 0.01' => ['0.031', '0.01', '0.021'],
            'round up 0.01 - 0.01' => ['0.01', '0.01', '0.000'],
            'round up 0.001 - 0.00' => ['0.001', '0.00', '0.001'],
        ];
    }
}