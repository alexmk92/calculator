<?php

namespace App\Services\Calculator\Expression;

class Expression
{
    /** @var Component[] $components */
    private $components;

    public function setComponents(Component ...$components): self
    {
        $this->components = $components;
        return $this;
    }

    public function evaluate(): float
    {
        return array_reduce($this->components, function ($carry, $component) {
            return $carry += $component->getValue();
        }, 0);
    }
}
