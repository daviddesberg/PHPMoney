<?php
namespace tests\PHPMoney\Unit\MathProvider;
use PHPMoney\MathProvider as Providers;

/**
 * @author David Desberg <david@daviddesberg.com>
 * Test case for the math providers
 */
class MathProvidersTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPMoney\MathProvider\MathProvider[] */
    private $mathProviders = [];

    public function setUp()
    {
        $this->mathProviders[] = new Providers\BCMathProvider();
        $this->mathProviders[] = new Providers\NativeMathProvider();
    }

    public function testAdd()
    {
        foreach($this->mathProviders as $mathProvider)
        {
            $providerName = get_class($mathProvider);
            $this->assertSame( '1500', $mathProvider->add('1495', '5'), "Addition test failed for $providerName" );
        }
    }

    public function testCompare()
    {
        foreach($this->mathProviders as $mathProvider)
        {
            $providerName = get_class($mathProvider);
            $this->assertSame( 1, $mathProvider->compare('1500', '5'), "Comparison (gt) test failed for $providerName" );
            $this->assertSame( -1, $mathProvider->compare('1500', '5000'), "Comparison (lt) test failed for $providerName" );
            $this->assertSame( 0, $mathProvider->compare('5000', '5000'), "Comparison (eq) test failed for $providerName" );
        }
    }

    public function testSubtract()
    {
        foreach($this->mathProviders as $mathProvider)
        {
            $providerName = get_class($mathProvider);
            $this->assertSame( '10', $mathProvider->subtract('100', '90'), "Subtraction test failed for $providerName" );
        }
    }

    public function testMultiplyNormal()
    {
        foreach($this->mathProviders as $mathProvider)
        {
            $providerName = get_class($mathProvider);
            $this->assertSame( '50', $mathProvider->multiply('5', '10'), "Multiplication (whole numbers) test failed for $providerName" );
            $this->assertSame( '50', $mathProvider->multiply('5', '10.09') );
            $this->assertSame( '49', $mathProvider->multiply('49.4', '1') );
            $this->assertSame( '51', $mathProvider->multiply('5', '10.11') );
        }
    }

    public function testMultiplyDecimalRoundDown()
    {
        foreach($this->mathProviders as $mathProvider)
        {
            $providerName = get_class($mathProvider);
            $this->assertSame( '0', $mathProvider->multiply('10', '0.05', $mathProvider::ROUND_MODE_HALF_DOWN), "Multiplication (with rounding halves down) test failed for $providerName" );
        }
    }

    public function testMultiplyDecimalRoundUp()
    {
        foreach($this->mathProviders as $mathProvider)
        {
            $providerName = get_class($mathProvider);
            $this->assertSame( '1', $mathProvider->multiply('10', '0.05', $mathProvider::ROUND_MODE_HALF_UP), "Multiplication (with rounding halves up) test failed for $providerName" );
        }
    }

    public function testMultiplyDecimalRoundHalfEven()
    {
        foreach($this->mathProviders as $mathProvider)
        {
            $providerName = get_class($mathProvider);
            $this->assertSame( '1', $mathProvider->multiply('10', '0.05', $mathProvider::ROUND_MODE_HALF_EVEN), "Multiplication (with rounding halves up if non decimal part is even) test failed for $providerName" );
            $this->assertSame( '1', $mathProvider->multiply('30', '0.05', $mathProvider::ROUND_MODE_HALF_EVEN), "Multiplication (with rounding halves up if non decimal part is even) test failed for $providerName" );
        }
    }

    public function testMultiplyDecimalRoundHalfOdd()
    {
        foreach($this->mathProviders as $mathProvider)
        {
            $providerName = get_class($mathProvider);
            $this->assertSame( '0', $mathProvider->multiply('10', '0.05', $mathProvider::ROUND_MODE_HALF_ODD), "Multiplication (with rounding halves up if non decimal part is odd) test failed for $providerName" );
            $this->assertSame( '2', $mathProvider->multiply('30', '0.05', $mathProvider::ROUND_MODE_HALF_ODD), "Multiplication (with rounding halves up if non decimal part is odd) test failed for $providerName" );
        }
    }

    public function testDivideNormal()
    {
        foreach($this->mathProviders as $mathProvider)
        {
            $providerName = get_class($mathProvider);
            $this->assertSame( '5', $mathProvider->divide('10', '2'), "Division (whole numbers) test failed for $providerName" );
        }
    }

    public function testMultiplyWithNegatives()
    {
        foreach($this->mathProviders as $mathProvider)
        {
            $providerName = get_class($mathProvider);
            $this->assertSame( '0', $mathProvider->multiply('10', '-0.04'), "Multiplication (with negative numbers) test failed for $providerName" );
            $this->assertSame( '-1', $mathProvider->multiply('10', '-0.06'), "Multiplication (with negative numbers) test failed for $providerName" );
        }
    }

    public function testMultiplyNoRound()
    {
        foreach($this->mathProviders as $mathProvider)
        {
            $providerName = get_class($mathProvider);
            $this->assertSame(16.76, (float) $mathProvider->multiply('10', '1.676', $mathProvider::ROUND_MODE_NONE) );
        }
    }

    public function testDivideNoRound()
    {
        foreach($this->mathProviders as $mathProvider)
        {
            $providerName = get_class($mathProvider);
            $this->assertSame(2.4, (float) $mathProvider->divide('24', '10', $mathProvider::ROUND_MODE_NONE) );
        }
    }

    public function testThrowsInvalidRoundingModeException()
    {
        foreach($this->mathProviders as $mathProvider)
        {
            $providerName = get_class($mathProvider);

            try {
                $mathProvider->multiply('10', '0.05', 324);
            } catch(Providers\Exception\InvalidRoundingModeException $e) {
                continue;
            }

            $this->fail('Rounding mode exception test (multiply) failed for provider ' . $providerName);
        }

        foreach($this->mathProviders as $mathProvider)
        {
            $providerName = get_class($mathProvider);

            try {
                $mathProvider->divide('10', '0.05', 324);
            } catch(Providers\Exception\InvalidRoundingModeException $e) {
                continue;
            }

            $this->fail('Rounding mode exception test (divide) failed for provider ' . $providerName);
        }
    }
}
