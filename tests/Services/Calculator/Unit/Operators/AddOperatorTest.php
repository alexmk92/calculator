<?php

use App\Services\Calculator\Operators\AddOperator;
use PHPUnit\Framework\TestCase;

final class AddOperatorTest extends TestCase
{
    protected $operator;

    public function setup(): void
    {
        parent::setup();
        $this->operator = new AddOperator();
    }

    public function testCanAddTwoWholeNumbers(): void
    {
        $this->assertEquals(15, $this->operator->evaluate(5, 10));
    }

    public function testCanAddThreeWholeNumbers(): void
    {
        $this->assertEquals(25, $this->operator->evaluate(5, 10, 10));
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
        $this->assertEquals(-5, $this->operator->evaluate(-10, 5));
        $this->assertEquals(-10, $this->operator->evaluate(-5, -5));
    }
}
