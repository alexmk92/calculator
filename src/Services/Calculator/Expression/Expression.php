<?php

namespace App\Services\Calculator\Expression;

use App\Services\Calculator\Operators\AddOperator;
use App\Services\Calculator\Operators\DivideOperator;
use App\Services\Calculator\Operators\IOperationContract;

class Expression
{
    /** @var [Component, IOperationContract][] $components */
    private $components = [];

    /**
     * Adds a new component with the defined join operator, by default this
     * will be the AddOperator.
     *
     * If this was a deferred component (caused by a "(" being detected in the parser,
     * then we will insert this component higher up the tree if the join operator
     * is a divide operation)
     *
     * @param Component $component
     * @param IOperationContract $joinOperator
     * @param bool $isDeferredComponent
     * @return Component
     */
    public function addComponent(Component $component, ?IOperationContract $joinOperator = null, bool $isDeferredComponent = false): Component
    {
        if (!$joinOperator) {
            $joinOperator = new AddOperator();
        }

        $component->setJoinOperator($joinOperator);

        $isDivideOperator = $joinOperator instanceof DivideOperator;
        $totalComponents  = count($this->components);

        if ($isDeferredComponent && $isDivideOperator && $totalComponents >= 1) {
            dd('here');
            array_splice($this->components, $totalComponents - 1, 0, $component);
        } else {
            $this->components[] = $component;
        }

        return $component;
    }

    /**
     * @return array
     */
    public function getComponents(): array
    {
        return $this->components;
    }

    /**
     * @return int
     */
    public function getComponentCount(): int
    {
        return count($this->components);
    }

    /**
     * @param int $index
     * @return null|Component
     */
    public function getComponent(int $id): ?Component
    {
        foreach ($this->components as $component) {
            if ($component->getId() === $id) {
                return $component;
            }
        }

        return null;
    }

    /**
     * @param int $index
     * @return null|Component
     */
    public function getComponentAtIndex(int $index): ?Component
    {
        if (isset($this->components[$index])) {
            return $this->components[$index];
        }

        return null;
    }

    /**
     * Attempts to remove the component from the expression, either
     * an int (the index which was removed) is returned, or null
     * if the component was not discoverd.
     *
     * @param int $index
     * @return int|null
     */
    public function removeComponent(int $id): ?int
    {
        foreach ($this->components as $idx => $component) {
            if ($component->getId() === $id) {
                unset($this->components[$idx]);
                return $idx;
            }
        }

        return null;
    }

    /**
     * Returns the value of this expression by summing all the components
     * based on each join operation.
     *
     * @return float
     */
    public function evaluate(): float
    {
        if (empty($this->components)) {
            return 0;
        }

        return array_reduce($this->components, function ($carry, $component) use (&$value) {
            // If we had a carryover component, its likely it had a left value
            // but no right value (as a '(' was encountered) in this situation
            // we need to balance the equation so the left depends on the previous
            // equation, we therefore return $carry as being the product
            // of the components value (as we need to recalc)
            if ($component->getRight() === null) {
                $component->setRight($component->getLeft());
                $component->setLeft($value);
                return $component->getValue();
            }

            $value = $component->getValue();
            return $component->getJoinOperator()->evaluate($carry, $value);
        }, 0);
    }
}
