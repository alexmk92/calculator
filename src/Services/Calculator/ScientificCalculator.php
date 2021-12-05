<?php

namespace App\Services\Calculator;

use App\Services\Calculator\Operators\OperatorList;

class ScientificCalculator extends BaseCalculator
{
    /**
     * @return IOperationContract[]
     */
    public function getOperators(): array
    {
        return [];
    }

}
