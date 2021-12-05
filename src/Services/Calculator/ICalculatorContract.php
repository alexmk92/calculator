<?php

namespace App\Services\Calculator;

interface ICalculatorContract
{
    public function evaluate(string ...$components): float;
}
