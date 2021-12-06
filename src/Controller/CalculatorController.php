<?php

namespace App\Controller;

use App\Services\Calculator\CalculatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CalculatorController extends AbstractController
{
    public function index(CalculatorService $calculatorService): Response
    {
        return new Response($calculatorService->render());
    }

    public function post(Request $request, CalculatorService $calculator): Response
    {
        $calculation = $calculator->evaluate($request->get('calculation'));
        $response = ['code' => 200, 'data' => $calculation];

        if (is_null($calculation)) {
            $response['code'] = 400;
        }

        return new Response(json_encode($response));
    }
}
