<?php

namespace App\Services\Calculator\Operators;

interface IOperationContract
{
    public function evaluate(float ...$components);
    public function getSymbolName(): string;
}
