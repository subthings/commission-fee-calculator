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
     * @param int $scale
     * @param string $expectation
     */
    public function testRoundUpCommission(string $number, int $scale, string $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->moneyCalculator->roundUpMoney($number, $scale)
        );
    }

    public function dataProviderForRoundUpCommissionTest(): array
    {
        return [
            'round up 0.023 with scale 2' => ['0.023', 2, '0.03'],
            'round up 0.0203 with scale 2' => ['0.0203', 2, '0.03'],
            'round up 0.05003 with scale 2' => ['0.05003', 2, '0.06'],
            'round up 0.0003 with scale 2' => ['0.0003', 2, '0.01'],
            'round up 0.6000 with scale 2' => ['0.6000', 2, '0.60'],
            'round up 6.6947 with scale 2' => ['6.6947', 2, '6.70'],
            'round up 0.600 with scale 1' => ['0.600', 1, '0.6'],
            'round up 6.694 with scale 1' => ['6.694', 1, '6.7'],
            'round up 0.01 with scale 0' => ['0.01', 0, '1'],
        ];
    }

    /**
     * @dataProvider dataProviderForRoundUpMulTest
     *
     * @param string $mul1
     * @param string $mul2
     * @param int $scale
     * @param string $expectation
     */
    public function testRoundUpMul(string $mul1, string $mul2, int $scale, string $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->moneyCalculator->roundUpMul($mul1, $mul2, $scale)
        );
    }

    public function dataProviderForRoundUpMulTest(): array
    {
        return [
            'round up 0.01 * 0.01 with scale 2' => ['0.01', '0.01', 2, '0.01'],
            'round up 0.10 * 0.10 with scale 2' => ['0.10', '0.10', 2, '0.01'],
            'round up 0.36 * 0.55 with scale 2' => ['0.36', '0.55', 2,  '0.20'],
            'round up 0.23 * 0.32 with scale 2' => ['0.23', '0.32', 2, '0.08'],
            'round up 0.01 * 0.01 with scale 1' => ['0.01', '0.01', 1, '0.1'],
            'round up 0.10 * 0.10 with scale 1' => ['0.10', '0.10', 1, '0.1'],
            'round up 0.36 * 0.55 with scale 1' => ['0.36', '0.55', 1,  '0.2'],
            'round up 0.23 * 0.32 with scale 1' => ['0.23', '0.32', 1, '0.1'],
            'round up 0.01 * 0.01 with scale 0' => ['0.01', '0.01', 0, '1'],
            'round up 0.10 * 0.10 with scale 0' => ['0.10', '0.10', 0, '1'],
            'round up 0.36 * 0.55 with scale 0' => ['0.36', '0.55', 0,  '1'],
            'round up 0.23 * 0.32 with scale 0' => ['0.23', '0.32', 0, '1'],
        ];
    }

    /**
     * @dataProvider dataProviderForRoundUpDivTest
     *
     * @param string $div1
     * @param string $div2
     * @param int $scale
     * @param string $expectation
     */
    public function testRoundUpDiv(string $div1, string $div2, int $scale, string $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->moneyCalculator->roundUpDiv($div1, $div2, $scale)
        );
    }

    public function dataProviderForRoundUpDivTest(): array
    {
        return [
            'round up 0.01 / 0.03 with scale 2' => ['0.01', '0.03', 2, '0.34'],
            'round up 0.01 / 0.01 with scale 2' => ['0.01', '0.01', 2, '1.00'],
            'round up 100.00 / 8.00 with scale 2' => ['100.01', '8.00', 2, '12.51'],
            'round up 0.01 / 0.03 with scale 1' => ['0.01', '0.03', 1, '0.4'],
            'round up 0.01 / 0.01 with scale 1' => ['0.01', '0.01', 1, '1.0'],
            'round up 100.00 / 8.00 with scale 1' => ['100.01', '8.00', 1, '12.6'],
            'round up 0.01 / 0.03 with scale 0' => ['0.01', '0.03', 0, '1'],
            'round up 0.01 / 0.01 with scale 0' => ['0.01', '0.01', 0, '1'],
            'round up 100.00 / 8.00 with scale 0' => ['100.01', '8.00', 0, '13'],
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