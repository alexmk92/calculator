<?php

namespace App\Services\Calculator;

use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session;

class CalculatorHistoryService
{
    /** @var string[] $history */
    private $history = [];
    /** @var Session $session */
    private $session;

    /**
     * By default, set a cookie that lasts for an hour.
     *
     * @param App\Services\Calculator\Environment $twig
     * @return void
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
        $this->history = json_decode($this->session->get('history', '{}'), true);
    }

    /**
     * @return array
     */
    public function getHistory(): array
    {
        return $this->history;
    }

    /**
     * @param string $expression
     * @param float $result
     * @return void
     */
    public function record(string $expression, float $result): void
    {
        $this->history[] = [
            'expression' => $expression,
            'result' => $result
        ];

        $this->updateSession();
    }

    /**
     * @return void
     */
    public function clear(?int $index = null): void
    {
        $history = &$this->history;

        if (is_null($index)) {
            $history = [];
        } else if (isset($history[$index])) {
            unset($history[$index]);
            $history = array_values($history);
        }

        $this->updateSession();
    }

    /**
     * @return void
     * @throws SessionNotFoundException
     */
    private function updateSession(): void
    {
        $this->session->set('history', json_encode($this->history));
    }
}
