<?php

namespace App\Services\Calculator;

use Exception;
use Twig\Environment;
use TypeError;

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
            return 0;
        } catch (TypeError $e) {
            return 0;
        }
    }

    public function render(float $expressionResult = 0): string
    {
        $controls = [[1, 2, 3], [4, 5, 6], [7, 8, 9], ['C', 0, '=']];
        $contents = $this->twig->render('calculator/index.html.twig', [
            'controls'  => $controls,
            'operators' => $this->getSupportedOperators(),
            'result'    => $expressionResult
        ]);

        return $contents;
    }
}
