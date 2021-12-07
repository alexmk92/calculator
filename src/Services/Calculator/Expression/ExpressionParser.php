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
    /** @var float|Component $currentLeft */
    private $currentLeft = null;
    /** @var float|Component $currentRight */
    private $currentRight = null;
    /** @var IOperationContract $currentOperator */
    private $currentOperator = null;
    /** @var Component[] $components */
    private $components = [];
    /** @var Expression[]  $expressions */
    private $expressions = [];
    /** @var bool $foundExpression */
    private $foundExpression = false;
    /** @var array $symbolDictionary */

    private $deferredComponents = [];

    public function __construct(ICalculatorContract $calculator)
    {
        $this->calculator = $calculator;

        foreach ($this->calculator->getOperators() as $operator) {
            $this->operatorList[$operator->getSymbol()] = $operator;
        }

        $this->symbolDictionary = array_flip(array_keys($this->operatorList));
    }

    /**
     * This is really messy, would optimise if I had more time, be kind :)
     *
     * @param string $input
     * @return Expression[]
     */
    public function parse(string $input)
    {
        $expressions = $this->extractExpressionsFromInput($input);
        $this->shutdown();

        return $expressions;
    }

    /**
     * Returns an array of expressions to be evaluated
     *
     * @param string $expressionString
     *
     * @return Expression[]
     */
    private function extractExpressionsFromInput(string $expressionString): array
    {
        $expressions     = &$this->expressions;
        $components      = &$this->components;
        $symbols         = implode('\\', array_keys($this->symbolDictionary));
        $expressionParts = preg_split("/([\($symbols\)])/", $expressionString, -1, PREG_SPLIT_DELIM_CAPTURE);

        do {
            $nextSymbol = array_shift($expressionParts);
            // We don't care about parsing spaces!
            if ($nextSymbol === '') {
                continue;
            }

            if ($this->matchNewExpression($nextSymbol)) {
                continue;
            }

            if ($this->matchLeftSymbol($nextSymbol)) {
                continue;
            }

            if ($this->matchOperatorSymbol($nextSymbol)) {
                continue;
            }

            // We don't care about continuing here as we want to attach the component
            // if we have finished this part of the expression
            $this->matchRightSymbol($nextSymbol);

            // We've discovered a component!
            if (is_numeric($this->currentLeft) && $this->currentOperator && is_numeric($this->currentRight)) {
                $components[] = new Component((float) $this->currentLeft, $this->currentOperator, (float) $this->currentRight);
                $this->resetPointers();
            }
        } while (count($expressionParts) > 0);

        // Ensure we attach any in-flight and valid work.
        $this->attachAdditionalComponentIfPresent();
        // Ensure we build our expression list
        if (!empty($this->components)) {
            $expressions[] = (new Expression())->setComponents(...$this->components);
        }

        return $expressions;
    }

    /**
     * Determines if we've matched a new expression or not - if we have
     * and there is a current component in-progress then we will defer
     * that for processing later!
     *
     * @param string|number $nextSymbol
     * @return bool
     */
    private function matchNewExpression($nextSymbol): bool
    {
        $components      = &$this->components;
        $expressions     = &$this->expressions;
        $foundExpression = &$this->foundExpression;

        if ($nextSymbol === '(') {
            $foundExpression = true;
            $this->deferCurrentComponent();
            return true;
        }

        // Detect if there was a valid found expression, if not skip as this
        // was some garbage input, like 3 + 3) - 2
        if ($nextSymbol === ')' && $foundExpression) {
            $this->attachAdditionalComponentIfPresent();
            $this->processDeferredComponent();
            $expressions[]   = (new Expression())->setComponents(...$components);
            $components      = [];
            $foundExpression = false;
            return true;
        } else if ($nextSymbol === ')') {
            return true;
        }

        return false;
    }

    /**
     * @param string|number $nextSymbol
     * @return bool
     */
    private function matchOperatorSymbol($nextSymbol): bool
    {
        if (isset($this->symbolDictionary[$nextSymbol])) {
            if (is_null($this->currentOperator)) {
                $this->currentOperator = $this->getOperatorInstance($nextSymbol);
            } else {
                $this->currentRight = $nextSymbol;
            }

            return true;
        }

        return false;
    }

    /**
     * @param string|number $nextSymbol
     * @return bool
     */
    private function matchRightSymbol($nextSymbol): bool
    {
        if (is_numeric($nextSymbol) && !is_numeric($this->currentRight)) {
            $this->currentRight .= $nextSymbol;
            return true;
        }

        return false;
    }

    /**
     * Determines if we made a match on the left symbol for this expression.
     *
     * @param string|number $nextSymbol
     * @return bool
     */
    private function matchLeftSymbol($nextSymbol): bool
    {
        $components = &$this->components;

        if (empty($components) && !is_numeric($this->currentLeft) && isset($this->symbolDictionary[$nextSymbol])) {
            $this->currentLeft = $nextSymbol;
            return true;
        }

        if (is_numeric($nextSymbol) && is_null($this->currentOperator)) {
            $this->currentLeft .= $nextSymbol;
            return true;
        }

        return false;
    }

    /**
     * @return void
     */
    private function shutdown()
    {
        // Ensure our pointers are reset for the next parse.
        $this->resetPointers();
        // Release our expression and component state
        $this->expressions = [];
        $this->components  = [];
    }

    /**
     * Defers the processing of this component as we've encountered an expression.
     *
     * @return void
     */
    private function deferCurrentComponent()
    {
        $this->deferredComponents[] = new Component($this->currentLeft, $this->currentOperator, $this->currentRight);
        $this->resetPointers();
    }

    /**
     * Resets all of the internal tracking pointers so we can track
     * the next expression evaluation.
     *
     * @return void
     */
    private function resetPointers()
    {
        $this->currentLeft     = null;
        $this->currentRight    = null;
        $this->currentOperator = null;
    }

    /**
     * When we get to an end of a sequence like 3 + 2 + 2 there is a carryover component of
     * `null + 2` which we need to attach to the previous component in the tree.
     *
     * This allows us to evaulate in the order of (5 + 2) as a Component can either
     * have a (float) or a (Component) as its left or right child.
     *
     * @param IOperationContract $currentOperator
     * @param null|float $currentRight
     * @return void
     */
    private function attachAdditionalComponentIfPresent()
    {
        $components = &$this->components;

        if ($this->currentOperator && $this->currentRight) {
            $left = count($components) ? $components[count($components) - 1] : 0;
            if (!is_numeric($left)) {
                unset($components[count($components)-1]);
            }

            $components[] = new Component($left, $this->currentOperator, (float) $this->currentRight);
        }
    }

    /**
     * Processes the next deferred component in the stack
     */
    private function processDeferredComponent(): bool
    {
        $component = array_shift($this->deferredComponents);
        if (!$component instanceof Component) {
            return true;
        }

        // A deferred component would have had a left value, but no right value
        // as it only becomes deferred if we encounter a '('.  We will however
        // be attaching this component to our previous one in the tree
        // and will need to set the deferredComponents left value
        // to our current right, so we can attach it to the
        // previous node.
        $this->currentOperator = $component->getOperator();
        $this->currentRight    = $component->getLeft();
        $this->attachAdditionalComponentIfPresent();
        $this->resetPointers();

        return true;
    }

    /**
     * @param string $operator
     * @return IOperationContract
     * @throws Exception
     */
    private function getOperatorInstance(string $operator): IOperationContract
    {
        if (empty(array_keys($this->operatorList))) {
            throw new Exception("{$operator} is not supported by this calculator");
        }

        return $this->operatorList[$operator];
    }
}
