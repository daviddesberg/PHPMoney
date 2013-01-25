<?php
namespace tests\PHPMoney\Unit;

/**
 * @author David Desberg <david@daviddesberg.com>
 * Test case for Money.
 */
use PHPMoney\MathProvider\BCMathProvider;
use PHPMoney\MathProvider\NativeMathProvider;
use PHPMoney\Money;

class MoneyTest extends \PHPUnit_Framework_TestCase
{

    private $mathProvider;

    public function setUp()
    {
        if( extension_loaded('bcmath') ) {
            $this->mathProvider = new BCMathProvider();
        } else {
            $this->mathProvider = new NativeMathProvider();
        }
    }

    public function testInvalidInitialization()
    {
        $this->setExpectedException('PHPMoney\\Exception\\InvalidArgumentException');
        new Money(65.34, $this->mathProvider);
    }

    public function testFormat()
    {
        $oneCent = new Money(1, $this->mathProvider);
        $oneDollar = new Money(100, $this->mathProvider);
        $oneBillionDollars = new Money('100000000000', $this->mathProvider);

        $this->assertSame( '0.01', $oneCent->format() );
        $this->assertSame( '1.00', $oneDollar->format() );
        $this->assertSame( '1,000,000,000.00', $oneBillionDollars->format() );

        // true,false doesn't matter for these
        $this->assertSame( '1', $oneCent->format(true, 0) );
        $this->assertSame( '10.0', $oneDollar->format(false, 1) );

    }

    public function testToString()
    {
        $oneDollar = new Money(100, $this->mathProvider);
        $this->assertSame( '100', (string) $oneDollar );
    }

    public function testAdd()
    {
        $oneDollar = new Money('100', $this->mathProvider);
        $twoDollar = new Money('200', $this->mathProvider);

        $threeDollar = $oneDollar->add($twoDollar);
        $this->assertSame( '300', (string) $threeDollar );
    }

    public function testSubtract()
    {
        $oneDollar = new Money('100', $this->mathProvider);
        $twoDollar = new Money('200', $this->mathProvider);

        $subtracted = $twoDollar->subtract($oneDollar);
        $this->assertSame( '100', (string) $subtracted );
    }

    public function testDivide()
    {
        $oneDollar = new Money('100', $this->mathProvider);
        $fiftyCents = $oneDollar->divide('2');
        $this->assertSame( '50', (string) $fiftyCents );

        $two = $oneDollar->divide( $fiftyCents );
        $this->assertSame(2.0, (float) $two);
    }

    public function testMultiply()
    {
        $oneDollar = new Money('100', $this->mathProvider);
        $twoDollars = $oneDollar->multiply(2);
        $this->assertSame( '200', (string) $twoDollars );
    }

    public function testNonsensicalMultiply()
    {
        $oneDollar = new Money('100', $this->mathProvider);
        $fiftyCents = new Money('50', $this->mathProvider);
        $this->setExpectedException('PHPMoney\\Exception\\InvalidMultiplicandException');
        $oneDollar->multiply($fiftyCents); // why would ya try to multiply money by money?
    }

    public function testComparisons()
    {
        $oneDollar1 = new Money('100', $this->mathProvider);
        $oneDollar2 = new Money('100', $this->mathProvider);

        $twoDollar1 = new Money('200', $this->mathProvider);

        $this->assertTrue( $oneDollar1->equals($oneDollar2) );
        $this->assertFalse( $oneDollar1->equals($twoDollar1) );

        $this->assertFalse( $oneDollar1->greaterThan($oneDollar2) );
        $this->assertFalse( $twoDollar1->lessThan($oneDollar1) );
        $this->assertFalse( $oneDollar1->lessThan($oneDollar2) );

        $this->assertTrue( $oneDollar1->lessThan($twoDollar1) );
        $this->assertTrue( $twoDollar1->greaterThan($oneDollar1) );
    }

}
