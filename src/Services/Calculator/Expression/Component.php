<?php

namespace App\Services\Calculator\Expression;

use App\Services\Calculator\Operators\AddOperator;
use App\Services\Calculator\Operators\IOperationContract;

class Component
{
    private static $id_seq = -1;
    /** @var int $id  */
    private $id;
    /** @var Component|float $left */
    private $left;
    /** @var Component|float $right */
    private $right;
    /** @var IOperationContract $operator */
    private $operator;
    /** @var IOperationContract $joinOperator */
    private $joinOperator;

    /**
     * @param float|Expression $value
     * @return void
     */
    public function __construct($left = null, ?IOperationContract $operator = null, $right = null, ?IOperationContract $joinOperator = null)
    {
        static::$id_seq++;

        $this->setLeft($left);
        $this->setRight($right);
        $this->setOperator($operator);
        $this->setJoinOperator($joinOperator);
        $this->id = static::$id_seq;
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
        $left  = $this->left instanceof Component ? $this->left->getValue() : $this->getLeft();
        $right = $this->right instanceof Component ? $this->right->getValue() : $this->getRight();

        // If someone entered something like +33 on its own, then this will evaluate to
        // either 0 or the value of left/right
        if (is_null($this->operator)) {
            return $this->getLeft() ?: $this->getRight();
        }

        return $this->operator->evaluate($left, $right);
    }

    /**
     * @return float|Component
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @return Component|float
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * @return null|IOperationContract
     */
    public function getOperator(): ?IOperationContract
    {
        return $this->operator;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Sets the join operator which other expressions can be concatenated to
     * with this operation.
     *
     * @return void
     */
    public function setJoinOperator(?IOperationContract $operator = null)
    {
        if (is_null($operator)) {
            $operator = new AddOperator();
        }

        $this->joinOperator = $operator;
    }

    /**
     * @return IOperationContract
     */
    public function getJoinOperator(): IOperationContract
    {
        return $this->joinOperator;
    }
}
