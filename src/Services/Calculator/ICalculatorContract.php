<?php

namespace App\Services\Calculator;

use App\Services\Calculator\Expression\ExpressionParser;
use App\Services\Calculator\Operators\IOperationContract;

interface ICalculatorContract
{
    /**
     * @return IOperationContract[]
     */
    public function getOperators(): array;

    public function evaluate(string $input): float;
}
