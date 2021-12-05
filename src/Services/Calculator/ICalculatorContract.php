<?php

namespace App\Services\Calculator;

use App\Services\Calculator\Operators\IOperationContract;
use App\Services\Calculator\Operators\OperatorList;

interface ICalculatorContract
{
    public function __construct(OperatorList $operators);

    /**
     * @return IOperationContract[]
     */
    public function getOperators(): array;
}
