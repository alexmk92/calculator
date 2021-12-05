<?php

namespace App\Services\Calculator;

use App\Services\Calculator\Operators\IOperationContract;
use App\Services\Calculator\Operators\OperatorList;

interface ICalculatorContract
{
    public function __construct(OperatorList $operators);
    public function evaluate(string ...$components): float;

    /**
     * @return IOperationContract[]
     */
    public function getOperators(): array;
}
