<?php

namespace App\Services\Calculator;

use App\Services\Calculator\Operators\AddOperator;
use App\Services\Calculator\Operators\DivideOperator;

class StandardCalculator extends BaseCalculator
{
    /**
     * @return IOperationContract[]
     */
    public function getOperators(): array
    {
        return [
            new AddOperator(),
            new DivideOperator(),
        ];
    }
}
