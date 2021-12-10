<?php

namespace App\Controller;

use App\Services\Calculator\CalculatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class CalculatorController extends AbstractController
{
    /** @var CalculatorService $calculatorService */
    private $calculatorService;

    public function __construct(CalculatorService $calculatorService, RequestStack $requestStack)
    {
        $this->calculatorService = $calculatorService;
        $this->requestStack      = $requestStack;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function index(): Response
    {
        return new Response($this->calculatorService->render());
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
