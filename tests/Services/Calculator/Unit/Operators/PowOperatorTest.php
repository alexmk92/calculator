<?php

use App\Services\Calculator\Operators\PowOperator;
use PHPUnit\Framework\TestCase;

final class PowOperatorTest extends TestCase
{
    protected $operator;

    public function setup(): void
    {
        parent::setup();
        $this->operator = new PowOperator();
    }

    public function testNumbersCanBeRaisedToAnExponent()
    {
        $this->assertEquals(81, $this->operator->evaluate(9, 2));
    }

    public function testThrowsWhenOneArgumentProvided()
    {
        try {
            $this->operator->evaluate(9);
        } catch (Exception $e) {
            $this->assertEquals("Pow expects exactly 2 numbers to be passed", $e->getMessage());
        }
    }

    public function testThrowsWhenMoreThanTwoArgumentsProvided()
    {
        try {
            $this->operator->evaluate(9, 1, 2);
        } catch (Exception $e) {
            $this->assertEquals("Pow expects exactly 2 numbers to be passed", $e->getMessage());
        }
    }
}
