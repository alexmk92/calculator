<?php

namespace App\Services\Calculator\Operators;

class SqrtOperator implements IOperationContract
{
    public function evaluate(float ...$components)
    {
        if (empty($components)) {
            return 0;
        }

        return sqrt(array_sum($components));
    }

    // Should probably implement a "View Symbol" method too
    // as the √ ascii char breaks my parser.
    public function getSymbol(): string
    {
        return '_';
    }
}
