<?php

use App\Services\Calculator\StandardCalculator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class StandardCalculatorTest extends KernelTestCase
{
    protected $calculator;

    public function setup(): void
    {
        static::bootKernel();

        // self::$container on the KernelTestCase exposes private services so we can test them
        // without modifying the service definition, woop! :D
        $this->calculator = self::$container->get(StandardCalculator::class);
    }

    public function testCanAddTwoWholeNumbers(): void
    {
        $this->assertEquals(4, $this->calculator->evaluate("2 + 2"));
    }

    public function testCanAddMultipleWholeNumbers(): void
    {
        $this->assertEquals(9, $this->calculator->evaluate("2 + 2 + 5"));
    }

    public function testCanDivideWholeNumbers(): void
    {
        $this->assertEquals(2.5, $this->calculator->evaluate("5 / 2"));
    }

    public function testCanMultiplyWholeNumbers(): void
    {
        $this->assertEquals(20, $this->calculator->evaluate("10 * 2"));
    }

    public function testCanSubtractNumbers(): void
    {
        $this->assertEquals(8, $this->calculator->evaluate("10 - 2"));
    }

    public function testCanCombineOperations(): void
    {
        $this->assertEquals(27.5, $this->calculator->evaluate("(5 * 11) / 2"));
    }

    public function testDivideByZeroReturnsE(): void
    {
        $this->assertEquals('E', $this->calculator->evaluate("10 / 0"));
    }
}
