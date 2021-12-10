# Dependencies
- PHP 7.4
- Composer 2+
- NodeJS 16+ (with yarn `npm install -g yarn`)

# Installation
`composer install && yarn && yarn dev`

## Running the application
`symfony server:start`

# Application
I've never used Symfony before, so please go easy on me, the results are from a 4 hour timeboxed session consulting the symfony docs. I've tried to use Dependency Injection where it made sense and I've modelled my unit tests against the directory structure of my Service.

I also decided to have some fun and try to implement my own tree that would parse a given equation.  At first this sounded like a great idea, but nested expression proved to be problematic, expect some equations to fail :) none of these are evaluated with the php's built in enumeration capabilities (BIDMAS is not respected unless parenthesis are applied).

## Cases covered:
- The calculator is tolerant to divide by zero
- The calculator maintains a history of past calculations via a Dependency Injected `CalculatorHistoryService`
- A standard calculator with `['(', ')', '+', '-', '*', '/']` operators
- A scientific calculator with the above operators as well as `['^', '_']` (pow and sqrt)
- The calculator maybe have additional dependencies injected and the UI will update - see `services.yml` and uncomment line 33 (commenting line 32) - this will give you an instance of a `ScientificCalculator` instead - you could also try and update the dependnecies of the `StandardCalculator` by registering the `PowOperator` (or try removing existing operators to limit the calculators capabilties)
- All operators have unit tests
- If you add parenthesis to an equation, you should be able to emulate order of operations
