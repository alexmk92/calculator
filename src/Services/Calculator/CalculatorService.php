<?php

namespace App\Services\Calculator;

use App\Services\Calculator\Expression\Expression;

class CalculatorService
{
    /** @var Expression $expression */
    private $expression;

    public function __construct(Expression $expression)
    {
        $this->expression = $expression;
    }

    public function evaluate(string $input): float
    {
        return $this->expression->evaluate($input);
    }
}
