<?php

namespace App\Services\Calculator;

use App\Services\Calculator\Operators\OperatorList;

class ScientificCalculator extends BaseCalculator
{
    public function evaluate(string ...$components): float
    {
        return 0;
    }

    /**
     * @return IOperationContract[]
     */
    public function getOperators(): array
    {
        return [];
    }

}
