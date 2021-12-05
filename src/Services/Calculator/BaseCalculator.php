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
}
