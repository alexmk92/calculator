<?php

namespace App\Services\Calculator;

class CalculatorService
{
    /** @var Calculator $calculator */
    private $calculator;

    public function __construct(ICalculatorContract $calculator)
    {
        $this->calculator = $calculator;
    }

    public function evaluate(string $input): float
    {
        return $this->calculator->evaluate($input);
    }
}
