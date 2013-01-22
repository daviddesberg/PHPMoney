<?php
namespace tests\PHPMoney\Unit;
use PHPMoney\MoneyFactory;

/**
 * @author David Desberg <david@daviddesberg.com>
 * Test case for MoneyFactory
 */
class MoneyFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var MoneyFactory */
    private $moneyFactory;

    public function setUp()
    {
        $this->moneyFactory = new MoneyFactory();
    }

    public function testMathProviderDetermination()
    {
        $explicitFactory = new MoneyFactory('NativeMathProvider');

        $reflClass = new \ReflectionClass( 'PHPMoney\\MoneyFactory' );
        $prop = $reflClass->getProperty('mathProvider');
        $prop->setAccessible(true);

        $this->assertInstanceOf('PHPMoney\\MathProvider\\NativeMathProvider', $prop->getValue($explicitFactory) );

        $guessFactory8Byte = new MoneyFactory(null, 8);
        $this->assertInstanceOf('PHPMoney\\MathProvider\\NativeMathProvider', $prop->getValue($guessFactory8Byte) );


        $guessFactory4ByteWithBCMath = new MoneyFactory(null, 4, true);
        $this->assertInstanceOf('PHPMoney\\MathProvider\\BCMathProvider', $prop->getValue($guessFactory4ByteWithBCMath) );

        $guessFactory4ByteWithoutBCMath = new MoneyFactory(null, 4, false);
        $this->assertInstanceOf('PHPMoney\\MathProvider\\NativeMathProvider', $prop->getValue($guessFactory4ByteWithoutBCMath) );
    }
    public function testCreateMoney()
    {
        $money = $this->moneyFactory->createMoney(100);
        $this->assertInstanceOf('PHPMoney\\Money', $money);
    }
}
