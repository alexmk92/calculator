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

    /** @var CalculatorHistoryService $calculatorHistory */
    private $calculatorHistoryService;

    public function __construct(ICalculatorContract $calculator, Environment $twig)
    {
        $this->calculator = $calculator;
        $this->twig       = $twig;
    }

    /**
     * @required
     * @param CalculatorHistoryService $calculatorHistoryService
     * @return void
     */
    public function setCalculatorHistory(CalculatorHistoryService $calculatorHistoryService)
    {
        $this->calculatorHistoryService = $calculatorHistoryService;
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

    /**
     * @param string $input
     * @return null|float
     */
    public function evaluate(string $input): ?float
    {
        try {
            $value = $this->calculator->evaluate($input);
        } catch (Exception $e) {
            $value = 0;
        } catch (TypeError $e) {
            $value = 0;
        }

        $this->calculatorHistoryService->record($input, $value);
        return $value;
    }

    public function render(): string
    {
        $controls   = [[1, 2, 3], [4, 5, 6], [7, 8, 9], ['C', 0, '='], ['(', '.', ')']];
        $history    = array_reverse($this->calculatorHistoryService->getHistory());
        $lastResult = empty($history) ? 0 : $history[0]['result'];

        $contents = $this->twig->render('calculator/index.html.twig', [
            'controls'  => $controls,
            'operators' => $this->getSupportedOperators(),
            'result'    => $lastResult,
            'history'   => $history
        ]);

        return $contents;
    }
}
