<?php

namespace App\Controller;

use App\Services\Calculator\CalculatorService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CalculatorController
{
    public function index(Request $request, CalculatorService $calculator): Response
    {
        $input = "10 + 12 + 2 + 5 / 2 + 2 + 3 / 2 + 5 * 6 + 2";
        // $calculator = (new CalculatorService())->withStandardFunctions();
        dd($calculator);
        dd($calculator->evaluate($input));

        return new Response(
            '<html><body>Lucky number: </body></html>'
        );
    }
}
