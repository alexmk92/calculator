<?php

namespace App\Services\Calculator\Operators;

class OperatorList
{
    /** @var Operator[] $operators */
    private $operators = [];

    public function __construct(IOperationContract ...$operators)
    {
        $this->addOperators(...$operators);
    }

    /**
     * @param IOperationContract[] $operators
     * @return void
     */
    public function addOperators(IOperationContract ...$operators)
    {
        foreach ($operators as $operator) {
            $this->operators[$operator->getSymbol()] = $operator;
        }
    }

    /**
     * Returns the requested operator.
     *
     * @param string $operator_name
     * @return null|IOperationContract
     */
    public function getOperator(string $operator_name): ?IOperationContract
    {
        if (isset($this->operators[$operator_name])) {
            return $this->operators[$operator_name];
        }

        return null;
    }
}
