<?php

// I've not used symfony before - wasn't sure how to override the DI container
// so that I could bind the scinetific calculator to it just for these
// unit tests

// use App\Services\Calculator\Expression\ExpressionParser;
// use App\Services\Calculator\ICalculatorContract;
// use App\Services\Calculator\ScientificCalculator;
// use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

// final class ScientificCalculatorTest extends KernelTestCase
// {
//     /** @var ScientificCalculator $calculator */
//     protected $calculator;

//     public function setup(): void
//     {
//         static::bootKernel();

//         $container = static::$kernel->getContainer();

//         $scientificCalculator = $container->get(ScientificCalculator::class);
//         $container->set(ICalculatorContract::class, $scientificCalculator);
//         $this->calculator = $scientificCalculator;
//     }

//     public function testCanRaiseNumberToExponent()
//     {
//         $this->assertEquals(81, $this->calculator->evaluate('9^2'));
//     }
// }
