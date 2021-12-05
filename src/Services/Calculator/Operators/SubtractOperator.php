<?php

namespace App\Services\Calculator\Operators;

class SubtractOperator implements IOperationContract
{
    public function evaluate(float ...$components)
    {
        if (empty($components)) {
            return 0;
        }

        // As we're subtracting, ensure we start from the left most
        // value and then start subtracting
        $start = $components[0];
        unset($components[0]);

        return array_reduce($components, function ($carry, $next) {
            $carry -= $next;
            return $carry;
        }, $start);
    }

    public function getSymbol(): string
    {
        return '-';
    }
}
