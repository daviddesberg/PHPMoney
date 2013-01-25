<?php
namespace PHPMoney;

/**
 * @author David Desberg <david@daviddesberg.com>
 */
class MoneyFactory
{
    private $mathProvider;

    /**
     * @param $mathProviderType string|null Type of math provider to create for the Money objects which will be created by this factory (string class name without namespace). If null, auto-determines.
     * @param $integerSize int The size of integer on this PHP install (defaults to PHP_INT_SIZE)
     * @param $hasBCMath bool|null Whether or not the BCMath extension is enabled, leave null to auto-determine.
     */
    public function __construct($mathProviderType = null, $integerSize = PHP_INT_SIZE, $hasBCMath = null)
    {
        if( null === $hasBCMath ) {
            $hasBCMath = extension_loaded('BCMath');
        }

        $mathProviderClass = 'PHPMoney\\MathProvider\\';

        if( is_string($mathProviderType) ) {
            $mathProviderClass .= $mathProviderType;
        } else {
            if( $integerSize >= 8 ) {
                $mathProviderClass .= 'NativeMathProvider';
            } elseif( $hasBCMath ) {
                $mathProviderClass .= 'BCMathProvider';
            } else {
                // forced into using native provider with 4 bit integers since no BCMath is available
                $mathProviderClass .= 'NativeMathProvider';
            }
        }

        $this->mathProvider = new $mathProviderClass();
    }

    /**
     * @param $value string|int Representation of the money value in the lowest denomination of that currency (for USD, cents. pence for GBP, etc.)
     * @return Money
     */
    public function createMoney($value)
    {
        return new Money($value, $this->mathProvider);
    }
}
