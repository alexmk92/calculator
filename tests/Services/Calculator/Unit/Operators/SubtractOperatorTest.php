<?php

use App\Services\Calculator\Operators\SubtractOperator;
use PHPUnit\Framework\TestCase;

final class SubtractOperatorTest extends TestCase
{
    protected $operator;

    public function setup(): void
    {
        parent::setup();
        $this->operator = new SubtractOperator();
    }

    public function testCanSubtractTwoWholeNumbers(): void
    {
        $this->assertEquals(10, $this->operator->evaluate(20, 10));
    }

    public function testCanSubtractDecimalNumbers(): void
    {
        $this->assertEquals(2.5, $this->operator->evaluate(5, 2.5));
    }

    public function testSingleValueInputYieldsInitialValue(): void
    {
        $this->assertEquals(5, $this->operator->evaluate(5));
    }

    public function testEmptyInputYieldsZero(): void
    {
        $this->assertEquals(0, $this->operator->evaluate());
    }

    public function testNegativeNumbersSumCorrectly(): void
    {
        $this->assertEquals(-5, $this->operator->evaluate(-10, -5));
        $this->assertEquals(0, $this->operator->evaluate(-5, -5));
    }
}
