<?php

namespace App\Services\Calculator\Expression;

use App\Services\Calculator\Operators\IOperationContract;

class Component
{
    private $left;
    private $right;

    /** @var IOperationContract $operator */
    private $operator;

    /**
     * @param float|Expression $value
     * @return void
     */
    public function __construct($left = null, ?IOperationContract $operator = null, $right = null)
    {
        $this->setLeft($left);
        $this->setRight($right);
        $this->setOperator($operator);
    }

    /**
     * @param null|Component|float $value
     * @return null|Component|float
     */
    private function getEnumerableComponent($value)
    {
        if ($value instanceof Component) {
            return $value;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        return null;
    }

    public function setLeft($left): self
    {
        $left = $this->getEnumerableComponent($left);

        if (!is_null($left)) {
            $this->left = $left;
        }

        return $this;
    }

    public function setRight($right): self
    {
        $right = $this->getEnumerableComponent($right);

        if (!is_null($right)) {
            $this->right = $right;
        }

        return $this;
    }

    public function setOperator(?IOperationContract $operator): self
    {
        if (!is_null($operator)) {
            $this->operator = $operator;
        }

        return $this;
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        $left  = $this->left instanceof Component ? $this->left->getValue() : $this->left;
        $right = $this->right instanceof Component ? $this->right->getValue() : $this->right;

        return $this->operator->evaluate($left, $right);
    }
}
