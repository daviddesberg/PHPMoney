PHPMoney
========

PHPMoney is a unit-tested Money pattern implementation (sans currency) for PHP 5.3+

## Usage ##
The library includes a `MoneyFactory` that will create `Money` instances for you using the best-available `MathProvider` that will work on your installation of PHP.

**Example Usage**

    <?php
    $factory = new PHPMoney\MoneyFactory();
    $twoDollars = $factory->createMoney(200);
    $threeDollars = $factory->createMoney(300);
    $fiveDollars = $twoDollars->add($threeDollars);
    echo $fiveDollars->format();

You can, of course, also create `Money` instances without the factory. You will need to pass each `Money` instance an instance of a `MathProvider` (you can and should use the same `MathProvider` instance for multiple `Money` objects).

	<?php
	$bcMathProvider = new PHPMoney\MathProvider\BCMathProvider();
	$oneDollar = new PHPMoney\Money(100, $bcMathProvider);
	$twoDollars = new PHPMoney\Money(200, $bcMathProvider);

## Tests ##
The tests are in the `tests` folder and reach 100% code-coverage. You need phpunit to run them. A `phpunit.xml` file is included.