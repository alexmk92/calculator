<?php

namespace App\Services\Calculator\Expression;

use App\Services\Calculator\ICalculatorContract;
use App\Services\Calculator\Operators\AddOperator;
use App\Services\Calculator\Operators\IOperationContract;
use Doctrine\ORM\Query\Expr;
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
    /** @var Expression  $expression */
    private $expression = null;
    /** @var bool $foundExpression */
    private $foundExpression = false;
    /** @var array $symbolDictionary */
    private $symbolDictionary = [];
    /** @var IOperationContract $joinOperation */
    private $joinOperation = null;
    /** @var int $deferredComponentRefCount */
    private $deferredComponentRefCount = -1;
    /** @var int $smybolIndex */
    private $symbolIndex = 0;

    private $deferredComponents = [];

    public function __construct(ICalculatorContract $calculator)
    {
        $this->calculator = $calculator;
        $this->expression = new Expression();

        foreach ($this->calculator->getOperators() as $operator) {
            $this->operatorList[$operator->getSymbol()] = $operator;
        }

        $this->symbolDictionary = array_flip(array_keys($this->operatorList));
    }

    /**
     * This is really messy, would optimise if I had more time, be kind :)
     *
     * @param string $input
     * @return Expression
     */
    public function parse(string $input): Expression
    {
        $symbols          = implode('\\', array_keys($this->symbolDictionary));
        $expressionString = str_replace(' ', '', $input);
        $expressionParts  = preg_split("/([\($symbols\)])/", $expressionString, -1, PREG_SPLIT_DELIM_CAPTURE);
        $expressionParts  = array_values(
            array_filter($expressionParts, function ($part) {
                return strlen(trim($part)) > 0;
            })
        );

        $expression = $this->extractExpressionsFromInput($expressionParts);
        $this->shutdown();

        return $expression;
    }

    /**
     * Returns an array of expressions to be evaluated
     *
     * @param string[] $expressionParts
     *
     * @return Expression
     */
    private function extractExpressionsFromInput(array $expressionParts): Expression
    {
        foreach ($expressionParts as $symbolIndex => $nextSymbol) {
            // Set so we can globally track which symbol we are at in helpers.
            $this->symbolIndex = $symbolIndex;
            // Ensure we get rid of any space on this symbol so we can evaluate
            // if it is numeric or not.
            $nextSymbol = trim($expressionParts[$this->symbolIndex]);
            // We don't care about parsing spaces!...this shouldn't happen, just a safety check
            if ($nextSymbol === '') {
                continue;
            }
            // If we start a new expression with ( then we up the reference count, this will
            // push the current component state onto a stack to be evaluated later, this
            // ensures order of operations is respected.
            if ($this->matchNewExpression($nextSymbol)) {
                continue;
            }
            // Seek ahead to set the join operator for this expression, this allows us to
            // control how the Expression class evaluates all Component combinations
            // by default the AddOperator will always be used.
            $prevSymbol = $expressionParts[max(0, $symbolIndex - 1)];
            if ($this->matchJoinSymbol($nextSymbol, $prevSymbol)) {
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
            if (!$this->matchRightSymbol($nextSymbol)) {
                continue;
            }
            // We've discovered a component!
            if (is_numeric($this->currentLeft) && $this->currentOperator && is_numeric($this->currentRight)) {
                $this->addComponent(new Component((float) $this->currentLeft, $this->currentOperator, (float) $this->currentRight));
                $this->resetPointers();
            }
        }
        // Ensure we attach any in-flight and valid work, this is normally once we've
        // evaluated nested conditions and need to evaluate
        $this->attachAdditionalComponentIfPresent();

        return $this->expression;
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
        if ($nextSymbol === '(') {
            $this->deferCurrentComponent();
            return true;
        }

        // Detect if there was a valid found expression, if not skip as this
        // was some garbage input, like 3 + 3) - 2
        if ($nextSymbol === ')') {
            $this->processDeferredComponent();
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
        if (is_numeric($nextSymbol) && !is_numeric($this->currentOperator)) {
            $this->currentRight .= $nextSymbol;
            return true;
        }

        return false;
    }

    /**
     * Tries to match the join symbol for the next component
     *
     * @param mixed $nextSymbol
     * @return bool
     */
    private function matchJoinSymbol($nextSymbol, $prevSymbol): bool
    {
        if ($this->symbolIndex === 0 && $nextSymbol === '-') {
            return false;
        }

        if ($this->symbolIndex > 0 && !in_array($prevSymbol, [')']) && is_null($this->currentOperator)) {
            return false;
        }

        if (isset($this->symbolDictionary[$nextSymbol])) {
            $this->joinOperation = $this->getOperatorInstance($nextSymbol);
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
        // If this is the first symbol and its a '-' then we're starting with a negative number.
        if ($this->symbolIndex === 0 && isset($this->symbolDictionary[$nextSymbol])) {
            if ($nextSymbol === '-') {
                $this->currentLeft = $nextSymbol;
            } else {
                $this->joinOperator = $this->getOperatorInstance($nextSymbol);
            }
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
        $this->symbolIndex = 0;

        $this->deferredComponentRefCount = -1;
    }

    /**
     * Defers the processing of this component as we've encountered an expression.
     * The reference count will always increase, to allow us to handle
     * nested calculation such as 2 + (9*2 + (12/3) / (4 * 2))
     *
     * We will assert this as (18 + 4 / 8) + 2 = 20.5
     *
     * @return void
     */
    private function deferCurrentComponent()
    {
        $this->deferredComponentRefCount++;

        if ($this->currentLeft) {
            $this->deferredComponents[] = new Component($this->currentLeft, $this->currentOperator, $this->currentRight);
        }

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
        if ($this->currentOperator && $this->currentRight) {
            $totalComponents = $this->expression->getComponentCount();
            $left            = $totalComponents > 0 ? $this->expression->getComponentAtIndex($totalComponents - 1) : 0;

            if ($left instanceof Component) {
                $this->expression->removeComponentAtIndex($totalComponents - 1);
                $left = $left->getValue();
            }

            $this->addComponent(new Component($left, $this->currentOperator, (float) $this->currentRight));
        } else if ($this->currentLeft && is_null($this->currentOperator)) {
            // If we just had an instance of a number, then attach a new component with this number
            // as its right property.  The expression will then evaluate component by attaching
            // the sum of previous components as the left child.
            $this->addComponent(new Component(null, null, $this->currentLeft));
        }
    }

    /**
     * Processes the next deferred component in the stack
     */
    private function processDeferredComponent(): bool
    {
        // $this->attachAdditionalComponentIfPresent();
        // Take the last component from the stack
        $ref = &$this->deferredComponentRefCount;
        $component = isset($this->deferredComponents[$ref]) ? $this->deferredComponents[$ref] : null;
        $ref--;

        if (!$component instanceof Component) {
            $this->resetPointers();
            return true;
        }

        $this->addComponent($component);
        // A deferred component would have had a left value, but no right value
        // as it only becomes deferred if we encounter a '('.  We will however
        // be attaching this component to our previous one in the tree
        // and will need to set the deferredComponents left value
        // to our current right, so we can attach it to the
        // previous node.
        $this->currentOperator = $component->getOperator();
        $this->currentRight    = $component->getLeft();
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

    /**
     * Adds a new component to the expression.
     *
     * @param Component $component
     * @return void
     */
    private function addComponent(Component $component)
    {
        $this->expression->addComponent($component, $this->joinOperation);
        // Always default back to the add operator
        $this->joinOperation = new AddOperator();
    }
}
