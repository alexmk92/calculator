<?php

namespace App\Services\Calculator\Expression;

use App\Services\Calculator\Component\Expression;
use Exception;

/**
 * Describes either a float, or another expression, this class acts as a single point
 * of returning a value to be used as part of an expression.
 *
 * @package App\Services\Calculator\Expression
 */
class Component
{
    private $component;
    private $value;

    /**
     * @param float|Expression $value
     * @return void
     */
    public function __construct($value)
    {
        if (is_numeric($value)) {
            $this->value = (float) $value;
        }

        if (!$value instanceof Expression || !is_float($value)) {
            throw new Exception("Component must either be an instance of Expression or a float");
        }
    }

    /**
     * Either return the numeric value stored in this instance, or if an
     * expression was passed to the constructor, calculate its value
     * and return it.
     *
     * @return float
     */
    public function getValue(): float
    {
        if ($this->component instanceof Expression) {
            $this->value = $this->component->evaluate();
        }

        return $this->value;
    }
}
