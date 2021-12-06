<?php

namespace App\Services\Calculator\Operators;

use Exception;

class PowOperator implements IOperationContract
{
    public function evaluate(float ...$components)
    {
        if (count($components) !== 2) {
            throw new Exception("Pow expects exactly 2 numbers to be passed");
        }

        return pow($components[0], $components[1]);
    }

    public function getSymbol(): string
    {
        return 'pow';
    }
}
