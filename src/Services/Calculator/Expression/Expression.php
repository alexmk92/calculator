<?php

namespace App\Services\Calculator\Expression;

use App\Services\Calculator\Expression\Component;
use App\Services\Calculator\Operators\IOperationContract;

/**
 * Expresses an interface to allow us to do things like (1 + 1)
 * or (1 + 1) * 2.  Each expression instance represents numbers
 * wrapped in parenthesis.
 *
 * @package App\Services\Calculator\Component
 */
class Expression
{
    /** @var Component $left */
    private $left;
    /** @var IOperationContract $operator */
    private $operator;
    /** @var Component $right */
    private $right;

    public function __construct(Component $left, IOperationContract $operator, Component $right)
    {
        $this->left = $left;
        $this->operator = $operator;
        $this->right = $right;
    }

    /**
     * Evaluates the returned value of this expression.
     *
     * @return float
     */
    public function evaluate(): float
    {
        return $this->operator->evaluate($this->left->getValue(), $this->right->getValue());
    }
}
