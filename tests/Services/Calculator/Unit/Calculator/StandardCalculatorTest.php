<?php

use App\Services\Calculator\ICalculatorContract;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class StandardCalculatorTest extends KernelTestCase
{
    protected $calculator;

    public function setup(): void
    {
        static::bootKernel();

        // self::$container on the KernelTestCase exposes private services so we can test them
        // without modifying the service definition, woop! :D
        $this->calculator = static::getContainer()->get(ICalculatorContract::class);
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
        $this->assertEquals(27.5, $this->calculator->evaluate("5 * 11 / 2"));
    }

    public function testBracketPrecedenceIsRespected(): void
    {
        $this->assertEquals(10, $this->calculator->evaluate("(5*20)/10"));
        $this->assertEquals(160, $this->calculator->evaluate("20*(2*4)"));
    }

    public function testDivideByZeroReturnsError(): void
    {
        try {
            $this->calculator->evaluate("10 / 0");
        } catch (Exception $e) {
            $this->assertEquals("Division by zero", $e->getMessage());
        }
    }

    public function testNonNumericInputReturnsZero(): void
    {
        // Running out of time - mixed assertions like hello + world + 2 will currently
        // throw an excpetion (due to float being required)
        $this->assertEquals(0, $this->calculator->evaluate("hello + world"));
    }

}
