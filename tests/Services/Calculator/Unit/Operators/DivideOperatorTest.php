<?php

use App\Services\Calculator\Operators\DivideOperator;
use PHPUnit\Framework\TestCase;

final class DivideOperatorTest extends TestCase
{
    protected $operator;

    public function setup(): void
    {
        parent::setup();
        $this->operator = new DivideOperator();
    }

    public function testCanDivideTwoNumbers(): void
    {
        $this->assertEquals(2.5, $this->operator->evaluate(5, 2));
    }

    public function testDivisionByZeroExceptionThrown(): void
    {
        try {
            $this->operator->evaluate(5, 0);
        } catch (Exception $e) {
            $this->assertEquals($e->getMessage(), "Division by zero");
        }
    }

    public function testTwoNumbersMustBeProvided(): void
    {
        try {
            $this->operator->evaluate(1);
        } catch (InvalidArgumentException $e) {
            $this->assertEquals($e->getMessage(), "Must provide two floating point numbers.");
        }

        try {
            $this->operator->evaluate(1, 2, 3);
        } catch (InvalidArgumentException $e) {
            $this->assertEquals($e->getMessage(), "Must provide two floating point numbers.");
        }
    }
}
