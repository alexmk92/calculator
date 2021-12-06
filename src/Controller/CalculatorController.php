<?php

namespace App\Controller;

use App\Services\Calculator\CalculatorService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CalculatorController
{
    public function get(Request $request, CalculatorService $calculator): Response
    {
        return new Response(
            '<html><body>Lucky number: </body></html>'
        );
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
