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

    /**
     * @dataProvider dataProviderForRoundUpMulTest
     *
     * @param string $mul1
     * @param string $mul2
     * @param string $expectation
     */
    public function testRoundUpMul(string $mul1, string $mul2, string $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->moneyCalculator->roundUpMul($mul1, $mul2)
        );
    }

    public function dataProviderForRoundUpMulTest(): array
    {
        return [
            'round up 0.01 * 0.01' => ['0.01', '0.01', '0.01'],
            'round up 0.10 * 0.10' => ['0.10', '0.10', '0.01'],
            'round up 0.36 * 0.55' => ['0.36', '0.55', '0.20'],
            'round up 0.23 * 0.32' => ['0.23', '0.32', '0.08'],
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
            $this->moneyCalculator->roundUpDiv($div1, $div2)
        );
    }

    public function dataProviderForRoundUpDivTest(): array
    {
        return [
            'round up 0.01 / 0.03' => ['0.01', '0.03', '0.34'],
            'round up 0.01 / 0.01' => ['0.01', '0.01', '1.00'],
            'round up 100.00 / 8.00' => ['100.01', '8.00', '12.51'],
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
            'round up 0.01 + 0.03' => ['0.01', '0.03', '0.04'],
            'round up 0.01 + 0.01' => ['0.01', '0.01', '0.02'],
            'round up 0.001 + 0.00' => ['0.001', '0.00', '0.00'],
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
            'round up 0.03 - 0.01' => ['0.03', '0.01', '0.02'],
            'round up 0.01 - 0.01' => ['0.01', '0.01', '0.00'],
            'round up 0.001 - 0.00' => ['0.001', '0.00', '0.00'],
        ];
    }
}