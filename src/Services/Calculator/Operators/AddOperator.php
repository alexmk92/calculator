<?php

namespace App\Services\Calculator\Operators;

class AddOperator implements IOperationContract
{
    public function evaluate(float ...$components)
    {
        return array_reduce($components, function ($carry, $next) {
            $carry += $next;
            return $carry;
        }, 0);
    }

    public function getSymbol(): string
    {
        return '+';
    }
}
