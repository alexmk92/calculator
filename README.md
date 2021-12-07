Built with PHP 7.4

Run with Symfony CLI `symfony server:start`

I've never used Symfony before, so please go easy on me, the results are from a 4 hour timeboxed session consulting the symfony docs. I've tried to use Dependency Injection where it made sense and I've modelled my unit tests against the directory structure of my Service.

## Limitations

I decided to have some fun with this and try to implement my own tree that would be able to parse mathematic equations, at first this sounded great but I quickly realised that trying to respect PEDMAS and parse out equations was a lot harder than I anticipated for my 4 hour timebox.

## Cases covered:

- The calculator is tolerant to divide by zero
- Division works
- Numbers can be raised by an exponent
- The calculator maybe have additional dependencies injected and the UI will update - see `services.yml` and uncomment line 33 (commenting line 32) - this will give you an instance of a `ScientificCalculator` instead - you could also try and update the dependnecies of the `StandardCalculator` by registering the `PowOperator` (or try removing existing operators to limit the calculators capabilties)
- All operators have unit tests
- If you add parenthesis to an equation, you should be able to emulate order of operations
