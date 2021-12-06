<?php

namespace App\Services\Calculator;

use App\Services\Calculator\Operators\AddOperator;
use App\Services\Calculator\Operators\DivideOperator;
use App\Services\Calculator\Operators\MultiplyOperator;
use App\Services\Calculator\Operators\PowOperator;
use App\Services\Calculator\Operators\SubtractOperator;

class ScientificCalculator extends BaseCalculator
{
    /**
     * @return IOperationContract[]
     */
    public function getOperators(): array
    {
        return [
            new AddOperator(),
            new DivideOperator(),
            new SubtractOperator(),
            new MultiplyOperator(),
            new PowOperator()
        ];
    }
}
