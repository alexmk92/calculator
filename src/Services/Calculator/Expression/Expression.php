<?php

namespace App\Services\Calculator\Expression;

use App\Services\Calculator\Operators\AddOperator;
use App\Services\Calculator\Operators\IOperationContract;

class Expression
{
    /** @var [Component, IOperationContract][] $components */
    private $components;

    /**
     * Adds a new component with the defined join operator, by default this
     * will be the AddOperator.
     *
     * @param Component $component
     * @param IOperationContract $joinOperator
     * @return void
     */
    public function addComponent(Component $component, ?IOperationContract $joinOperator = null)
    {
        if (!$joinOperator) {
            $joinOperator = new AddOperator();
        }

        $this->components[] = [
            'component' => $component,
            'operator'  => $joinOperator
        ];
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
    public function getComponentAtIndex(int $index): ?Component
    {
        if (isset($this->components[$index])) {
            return $this->components[$index]['component'];
        }

        return null;
    }

    /**
     * @param int $index
     * @return void
     */
    public function removeComponentAtIndex(int $index)
    {
        if (isset($this->components[$index])) {
            unset($this->components[$index]);
            $this->components = array_values($this->components);
        }
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

        return array_reduce($this->components, function ($carry, $componentOperator) {
            /** @var IOperationContract $operator */
            $operator  = $componentOperator['operator'];
            /** @var Component $component */
            $component = $componentOperator['component'];

            return $operator->evaluate($carry, $component->getValue());
        }, 0);
    }
}
