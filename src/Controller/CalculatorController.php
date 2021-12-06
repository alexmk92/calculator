<?php

namespace App\Controller;

use App\Services\Calculator\CalculatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CalculatorController extends AbstractController
{
    public function index(Request $request, CalculatorService $calculatorService): Response
    {
        $expressionResult = $request->get('expression_result', 0);
        return new Response($calculatorService->render($expressionResult));
    }

    public function post(Request $request, CalculatorService $calculator): RedirectResponse
    {
        $calculation = $calculator->evaluate($request->get('expression'));

        $params = [];
        if (!is_null($calculation)) {
            $params = ['expression_result' => $calculation];
        }

        return $this->redirectToRoute('index', $params);
    }
}
