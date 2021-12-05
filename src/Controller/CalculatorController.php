<?php

namespace App\Controller;

use App\Services\Calculator\CalculatorService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CalculatorController
{
    public function index(Request $request, CalculatorService $calculator): Response
    {
        // $calculator = (new CalculatorService())->withStandardFunctions();
        dd($calculator);

        return new Response(
            '<html><body>Lucky number: </body></html>'
        );
    }
}
