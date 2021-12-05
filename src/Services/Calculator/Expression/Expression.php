<?php

namespace App\Services\Calculator\Expression;

class Expression
{
    /** @var ExpressionParser $expressionParser */
    private $expressionParser;
    /** @var Component[] $components */
    private $components = [];

    public function __construct(ExpressionParser $expressionParser)
    {
        $this->expressionParser = $expressionParser;
    }

    public function evaluate(string $input): float
    {
        $this->components = $this->expressionParser->parse($input);

        return array_reduce($this->components, function ($carry, $component) {
            return $carry += $component->getValue();
        }, 0);
    }
}
