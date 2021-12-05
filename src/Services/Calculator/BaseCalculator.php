<?php

namespace App\Services\Calculator;

use App\Services\Calculator\Operators\OperatorList;

abstract class BaseCalculator implements ICalculatorContract
{
    /** @var OperatorList $operator_list */
    protected $operator_list;

    public function __construct(OperatorList $operator_list)
    {
        $this->operator_list = $operator_list;
        $this->operator_list->addOperators(...$this->getOperators());
    }

    /**
     * Returns an operator from the operator list if its set.
     *
     * @param string $operator_name
     * @return null|ICalculatorContract
     */
    public function getOperator(string $operator_name): ?ICalculatorContract
    {
        return $this->operator_list->getOperator($operator_name);
    }

    public function getOperatorList(): OperatorList
    {
        return $this->operator_list;
    }
}
