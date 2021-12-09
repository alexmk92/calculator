<?php

namespace App\Services\Calculator;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class CalculatorHistoryService
{
    /** @var string[] $history */
    private $history = [];
    /** @var Environment $twig */
    private $twig;
    /** @var int $expires */
    private $expires;

    /**
     * By default, set a cookie that lasts for an hour.
     *
     * @param App\Services\Calculator\Environment $twig
     * @param int $expires
     * @return void
     */
    public function __construct(Environment $twig, $expires = 3600)
    {
        $this->twig     = $twig;
        $this->expires  = time() + $expires;
    }

    /**
     * Generates a new cookie so we can render the history client
     * side.
     *
     * @return Cookie
     * @throws InvalidArgumentException
     */
    private function setHistoryCookie(): Cookie
    {
        return Cookie::create('calculationHistory')
            ->withValue(json_encode($this->history))
            ->withExpires($this->expires);
    }

    /**
     * @param string $expression
     * @param float $result
     * @return void
     */
    public function setValue(string $expression, float $result)
    {
        $this->history[] = [
            'expression' => $expression,
            'result' => $result
        ];
    }

    /**
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function render()
    {
        $this->response->headers->setCookie($this->setHistoryCookie());
        return $this->twig->render('calculator/history.twig.html', [

        ]);
    }
}
