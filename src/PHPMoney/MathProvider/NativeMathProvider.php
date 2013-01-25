<?php
namespace PHPMoney\MathProvider;
use PHPMoney\MathProvider\Exception\InvalidRoundingModeException;

/**
 * @author David Desberg <david@daviddesberg.com>
 */
class NativeMathProvider implements MathProvider
{
    /**
     * @param $a string String representation of a value of the lowest unit of currency (i.e. cents). Must be whole number.
     * @param $b string String representation of cents. Must be whole number.
     * @return string String representation of a + b
     */
    function add($a, $b)
    {
        return (string) ( (int) $a + (int) $b );
    }

    /**
     * @param $a string String representation of a value of the lowest unit of currency (i.e. cents). Must be whole number.
     * @param $b string String representation of cents. Must be whole number.
     * @return string String representation of a - b
     */
    function subtract($a, $b)
    {
        return (string) ( (int) $a - (int) $b );
    }

    /**
     * @param $a string String representation of a value of the lowest unit of currency (i.e. cents). Must be whole number.
     * @param $b string String representation of multiplicand
     * @param $roundingMode int A rounding mode constant.
     * @throws InvalidRoundingModeException
     * @return string String representation of a * b. Will be a whole number based upon rounding mode.
     */
    function multiply($a, $b, $roundingMode = self::ROUND_MODE_DEFAULT)
    {
        if( self::ROUND_MODE_NONE === $roundingMode ) {
            return (string) ( (int) $a * (float) $b );
        }
        if( !in_array( $roundingMode, array( self::ROUND_MODE_HALF_EVEN, self::ROUND_MODE_HALF_ODD, self::ROUND_MODE_HALF_DOWN, self::ROUND_MODE_HALF_UP ) ) ) {
            throw new InvalidRoundingModeException("Invalid rounding mode '{$roundingMode}' provided");
        }

        return (string) ( (int) round( (int) $a * (float) $b, 0, $roundingMode ) );
    }

    /**
     * @param $a string String representation of a value of the lowest unit of currency (i.e. cents). Must be whole number.
     * @param $b string String representation of divisor
     * @param $roundingMode int A rounding mode constant.
     * @throws InvalidRoundingModeException
     * @return string String representation of a / b. Will be a whole number based upon rounding mode
     */
    function divide($a, $b, $roundingMode = self::ROUND_MODE_DEFAULT)
    {
        if( self::ROUND_MODE_NONE === $roundingMode ) {
            return (string) ( (int) $a / (float) $b );
        }
        if( !in_array( $roundingMode, array( self::ROUND_MODE_HALF_EVEN, self::ROUND_MODE_HALF_ODD, self::ROUND_MODE_HALF_DOWN, self::ROUND_MODE_HALF_UP ) ) ) {
            throw new InvalidRoundingModeException("Invalid rounding mode '{$roundingMode}' provided");
        }
        return (string) ( (int) round( (int) $a / (float) $b, 0, $roundingMode ) );
    }

    /**
     * @param $a string String representation of a numeric value.
     * @param $b string String representation of a numeric value.
     * @return int
     */
    function compare($a, $b)
    {
        $a = (int) $a;
        $b = (int) $b;
        if( $a === $b ) {
            return 0;
        } elseif( $a < $b ) {
            return -1;
        } else {
            return 1;
        }
    }
}
