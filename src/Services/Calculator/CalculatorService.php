<?php

namespace App\Services\Calculator;

use App\Services\Calculator\ICalculatorContract;

class CalculatorService
{
    /** @var ICalculatorContract $calculator */
    private $calculator = null;

    public function __construct(ICalculatorContract $calculator)
    {
        $this->calculator = $calculator;
    }

    public function evaluate(string ...$expression): float
    {
        return $this->calculator->evaluate(...$expression);
    }
}
