<?php

use App\Services\Calculator\Operators\PowOperator;
use App\Services\Calculator\Operators\SqrtOperator;
use PHPUnit\Framework\TestCase;

final class SqrtOperatorTest extends TestCase
{
    protected $operator;

    public function setup(): void
    {
        parent::setup();
        $this->operator = new SqrtOperator();
    }

    public function testSqrtCanBeCalculated()
    {
        $this->assertEquals(12, $this->operator->evaluate(144));
    }

    public function testSqrtOfSumCanBeCalculated()
    {
        $this->assertEquals(12, $this->operator->evaluate(12, 100, 32));
    }

    public function testSqrtOfNothingIsZero()
    {
        $this->assertEquals(0, $this->operator->evaluate());
    }
}
