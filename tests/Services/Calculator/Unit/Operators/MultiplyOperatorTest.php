<?php

use App\Services\Calculator\Operators\MultiplyOperator;
use PHPUnit\Framework\TestCase;

final class MultiplyOperatorTest extends TestCase
{
    protected $operator;

    public function setup(): void
    {
        parent::setup();
        $this->operator = new MultiplyOperator();
    }

    public function testCanMultiplyTwoWholeNumbers(): void
    {
        $this->assertEquals(10, $this->operator->evaluate(5, 2));
    }

    public function testCanMultiplyDecimalNumbers(): void
    {
        $this->assertEquals(5, $this->operator->evaluate(2.5, 2));
    }

    public function testSingleValueInputYieldsInitialValue(): void
    {
        $this->assertEquals(5, $this->operator->evaluate(5));
    }

    public function testEmptyInputYieldsZero(): void
    {
        $this->assertEquals(0, $this->operator->evaluate());
    }

    public function testMultiplyingByZeroIsZero(): void
    {
        $this->assertEquals(0, $this->operator->evaluate(0, 0));
    }

    public function testNegativeNumbersSumCorrectly(): void
    {
        $this->assertEquals(50, $this->operator->evaluate(-10, -5));
        $this->assertEquals(25, $this->operator->evaluate(-5, -5));
    }
}
