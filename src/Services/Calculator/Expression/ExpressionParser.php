<?php

namespace App\Services\Calculator\Expression;

use App\Services\Calculator\ICalculatorContract;

class ExpressionParser
{
    private $calculator;

    public function __construct(ICalculatorContract $calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * This is really messy, would optimise if I had more time, be kind :)
     *
     * @param string $input
     * @return array
     */
    public function parse(string $input)
    {
        $components = [];

        $operators = [];
        foreach ($this->calculator->getOperators() as $operator) {
            $operators[$operator->getSymbol()] = $operator;
        }

        $expression = explode(' ', $input);

        $possible_components = array_chunk($expression, 3);
        foreach ($possible_components as $possible_component) {
            // If we don't have a left, operator and right - then continue
            // unless we have 2, in which case we will use the previous
            // component as the left node.
            $component_parts = count($possible_component);
            if ($component_parts < 2) {
                continue;
            }

            $left     = $possible_component[0];
            $operator = $possible_component[1];
            $right    = $component_parts === 3 ? $possible_component[2] : null;

            if (!is_numeric($left)) {
                // In the example of 1 + 2 + 3 + 5 / 2 + 2
                // we would have the chunks (1 + 2) (+ 3 +) (5 / 2) (+ 2)
                // we will build a tree-like structure of components to
                // sum if we encounter a node that is in the (+ N +) format
                //                     1 + 2             5 / 2
                //                       | + 3             | + 2
                //                      ------------------------
                //                                10.5
                // I don't believe this respects PEDMAS, it was an oversight
                // by me when building out the DI container.
                $tmp_op   = $operator;
                $operator = $left;
                $left     = $components[count($components) - 1];
                $right    = $tmp_op;

                unset($components[count($components) - 1]);
            }

            // Discard this component if our calculator doesn't support it.
            if (!isset($operators[$operator])) {
                continue;
            }

            $components[] = new Component($left, $operators[$operator], $right);
        }

        return $components;
    }
}
