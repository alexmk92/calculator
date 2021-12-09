<?php

namespace App\Controller;

use App\Services\Calculator\CalculatorHistoryService;
use App\Services\Calculator\CalculatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\Error\RuntimeError;

class CalculatorController extends AbstractController
{
    /** @var CalculatorService $calculatorService */
    private $calculatorService;
    /** @var CalculatorHistoryService $calculatorHistoryService */
    private $calculatorHistoryService;
    /** @var RequestStack $requestStack */
    private $requestStack;

    public function __construct(CalculatorService $calculatorService, CalculatorHistoryService $calculatorHistoryService, RequestStack $requestStack)
    {
        $this->calculatorService        = $calculatorService;
        $this->calculatorHistoryService = $calculatorHistoryService;
        $this->requestStack             = $requestStack;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $expressionResult = $request->get('expression_result', 0);
        return new Response($this->calculatorService->render($expressionResult));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function post(Request $request): RedirectResponse
    {
        $expression = $request->get('expression', 0);
        // Could do more rigorous evaluation here with a custom validator
        // such as checking mathematic symbols and no non-numeric
        // characters are present.
        if (!empty($expression)) {
            $this->calculatorService->evaluate($expression);
        }

        return $this->redirectToRoute('index');
    }
}
