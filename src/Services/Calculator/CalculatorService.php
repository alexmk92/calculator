<?php

namespace App\Services\Calculator;

use Exception;
use Twig\Environment;

class CalculatorService
{
    /** @var Environment $twig */
    private $twig;

    /** @var ICalculatorContract $calculator */
    private $calculator;

    public function __construct(ICalculatorContract $calculator, Environment $twig)
    {
        $this->calculator = $calculator;
        $this->twig       = $twig;
    }

    /**
     * Returns an array of all supported operators.
     *
     * @return string[]
     */
    public function getSupportedOperators(): array
    {
        return array_map(function ($operator) {
            return $operator->getSymbol();
        }, $this->calculator->getOperators());
    }

    public function evaluate(string $input): ?float
    {
        try {
            return $this->calculator->evaluate($input);
        } catch (Exception $e) {
            return null;
        }
    }

    public function render(): string
    {
        $contents = $this->twig->render('calculator/index.html.twig', [
            'operators' => $this->getSupportedOperators(),
        ]);

        return $contents;
    }
}
