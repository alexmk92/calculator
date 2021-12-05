<?php

namespace App\Services\Calculator\Operators;

use Exception;
use InvalidArgumentException;

class DivideOperator implements IOperationContract
{
    public function evaluate(float ...$components): float
    {
        if (count($components) !== 2) {
            throw new InvalidArgumentException("Must provide two floating point numbers.");
        }

        $left  = $components[0];
        $right = $components[1];

        if ($right === 0) {
            throw new Exception("Cannot divide by zero.");
        }

        return $left / $right;
    }

    public function getSymbolName(): string
    {
        return 'divide';
    }
}
