<?php

namespace App\Services\Calculator;

use App\Services\Calculator\Expression\Expression;
use App\Services\Calculator\Expression\ExpressionParser;
use App\Services\Calculator\Operators\OperatorList;

abstract class BaseCalculator implements ICalculatorContract
{
    /** @var OperatorList $operator_list */
    protected $operator_list;
    /** @var ExpressionParser $expressionParser */
    private $expressionParser;

    public function __construct()
    {
        $this->expressionParser = new ExpressionParser($this);
    }

    /**
     * @required
     */
    public function setOperatorList(OperatorList $operator_list)
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

    /**
     * Evaluate the result of the expression by parsing it based
     * on the supported operators of this calculator.
     *
     * @param string $input
     * @return float
     *
     * @throws Exception $e - when dividing by zero we will get an excpetion
     */
    public function evaluate(string $input): float
    {
        /** @var Expression $expression */
        return $this->expressionParser->parse($input)->evaluate();
    }
}
