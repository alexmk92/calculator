<?php

namespace App\Services\Calculator\Expression;

use App\Services\Calculator\ICalculatorContract;
use App\Services\Calculator\Operators\IOperationContract;
use Exception;

class ExpressionParser
{
    /** @var ICalculatorContract $calculator */
    private $calculator;
    /** @var IOperationContract[] $operatorList */
    private $operatorList = [];

    public function __construct(ICalculatorContract $calculator)
    {
        $this->calculator = $calculator;

        foreach ($this->calculator->getOperators() as $operator) {
            $this->operatorList[$operator->getSymbol()] = $operator;
        }
    }

    /**
     * This is really messy, would optimise if I had more time, be kind :)
     *
     * @param string $input
     * @return Expression[]
     */
    public function parse(string $input)
    {
        $expressionStrings = $this->extractExpressionStrings($input);
        $expressions       = [];

        /** @var Expression $previousExpression */
        $previousExpression = null;

        foreach ($expressionStrings as $expressionIndex => $expressionString) {
            $expressionString = trim(str_replace(['(', ')', ' '], '', $expressionString));
            $components = $this->buildComponentListFromExpressionString($expressionString);
            $expression = (new Expression())->setComponents(...$components);

            if ($previousExpression !== null && $components[0]->getLeft() === null) {
                $components[0]->setLeft($previousExpression->evaluate());
                unset($expressions[$expressionIndex - 1]);
            }

            $expressions[]      = $expression;
            $previousExpression = $expression;
        }

        return $expressions;
    }

    /**
     * Returns an array of components to be evaluated
     *
     * @param string $expressionString
     * @param Component[] $components
     */
    private function buildComponentListFromExpressionString(string $expressionString): array
    {
        $components         = [];
        $symbols            = implode('\\', array_keys($this->operatorList));
        $expressionParts    = preg_split("/([$symbols])/", $expressionString,-1, PREG_SPLIT_DELIM_CAPTURE);
        $possibleComponents = array_chunk($expressionParts, 3);

        foreach ($possibleComponents as $possibleComponent) {
            // If we don't have a left, operator and right - then continue
            // unless we have 2, in which case we will use the previous
            // component as the left node.
            $componentParts = count($possibleComponent);
            if ($componentParts < 2) {
                continue;
            }

            if ($componentParts === 3) {
                $left     = is_numeric($possibleComponent[0]) ? $possibleComponent[0] : null;
                $operator = is_numeric($possibleComponent[1]) ? $possibleComponent[0] : $possibleComponent[1];
                $right    = is_numeric($possibleComponent[2]) ? $possibleComponent[2] : $possibleComponent[1];
            } else if ($componentParts === 2) {
                $left     = null;
                $operator = is_numeric($possibleComponent[0]) ? $possibleComponent[1] : $possibleComponent[0];
                $right    = is_numeric($possibleComponent[0]) ? $possibleComponent[0] : $possibleComponent[1];
            }

            $operatorInstance = $this->getOperatorInstance($operator);
            $totalComponents  = count($components);
            if (is_null($left) && $totalComponents > 0) {
                $last_component = $components[count($components) - 1];
                $component = new Component($last_component, $operatorInstance, $right);
                unset($components[count($components) -1]);
                $components = array_values($components);
            } else {
                $component = new Component($left, $operatorInstance, $right);
            }

            if (!is_null($component)) {
                $components[] = $component;
            }
        }

        return $components;
    }

    protected function getOperatorInstance(string $operator): IOperationContract
    {
        if (empty(array_keys($this->operatorList))) {
            throw new Exception("{$operator} is not supported by this calculator");
        }

        return $this->operatorList[$operator];
    }

    /**
     * This calculator is pretty dumb as its implementing its own PEDMAS
     * (pretty bad)...I wanted to demo Dependency Injection and
     * services - so here's a really dumb implementation
     * of evaluating brackets to give correct order
     * of operations...each expression is extracted
     * from the input string and returned for
     * evaluation by its IOperationContract
     *
     * @param string $input
     * @return string[]
     * @throws Exception
     */
    protected function extractExpressionStrings(string $input): array
    {
        // Ensure we have the formula wrapped in brackets, unless
        // it has already been wrapped.
        if (strpos($input, '(', 0) !== 0) {
            $input = "({$input})";
        }

        preg_match_all("(\(([ ]*\d+ ?[ ]*([\+-\/*][ ]*\d+[ ]*)*)\)|([ ]*[\+-\/*][ ]*\d+?[ ]*)?)", $input, $matches);

        if (empty($matches)) {
            throw new Exception("Empty expression detected");
        }

        return array_filter($matches[0], function ($match) {
            return !empty($match);
        });
    }
}
