<?php

namespace App\Services\Calculator;

use Exception;

class CalculatorService
{
    /** @var ICalculatorContract $calculator */
    private $calculator;

    public function __construct(ICalculatorContract $calculator)
    {
        $this->calculator = $calculator;
    }

    public function evaluate(string $input): ?float
    {
        try {
            return $this->calculator->evaluate($input);
        } catch (Exception $e) {
            return null;
        }
    }
}
