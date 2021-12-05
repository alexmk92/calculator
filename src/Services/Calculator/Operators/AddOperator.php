<?php

namespace App\Services\Calculator\Operators;

class AddOperator implements IOperationContract
{
    public function evaluate(float ...$components)
    {
        $sum = array_reduce($components, function ($carry, $next) {
            $carry += $next;
            return $carry;
        }, 0);

        return $sum;
    }

    public function getSymbolName(): string
    {
        return 'add';
    }
}
