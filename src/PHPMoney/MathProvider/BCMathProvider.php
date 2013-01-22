<?php
namespace PHPMoney\MathProvider;
use PHPMoney\MathProvider\Exception\InvalidRoundingModeException;

/**
 * @author David Desberg <david@daviddesberg.com>
 */
class BCMathProvider implements MathProvider
{
    /**
     * @param $a string String representation of a numeric value.
     * @param $b string String representation of a numeric value.
     * @return int
     */
    function compare($a, $b)
    {
        return bccomp($a, $b, 0);
    }

    /**
     * @param $a string String representation of a value of the lowest unit of currency (i.e. cents). Must be whole number.
     * @param $b String representation of cents. Must be whole number.
     * @return string String representation of a + b
     */
    function add($a, $b)
    {
        return bcadd($a, $b, 0);
    }

    /**
     * @param $a string String representation of a value of the lowest unit of currency (i.e. cents). Must be whole number.
     * @param $b String representation of cents. Must be whole number.
     * @return string String representation of a - b
     */
    function subtract($a, $b)
    {
        return bcsub($a, $b, 0);
    }

    /**
     * @param $a string String representation of a value of the lowest unit of currency (i.e. cents). Must be whole number.
     * @param $b string String representation of multiplicand
     * @param $roundingMode int A rounding mode constant.
     * @return string String representation of a * b. Will be a whole number based upon rounding mode.
     */
    function multiply($a, $b, $roundingMode = self::ROUND_MODE_DEFAULT)
    {
        $result = bcmul($a, $b, 1);
        return $this->round($result, $roundingMode);
    }

    /**
     * @param $a string String representation of a value of the lowest unit of currency (i.e. cents). Must be whole number.
     * @param $b string String representation of divisor
     * @param $roundingMode int A rounding mode constant.
     * @return string String representation of a / b. Will be a whole number based upon rounding mode
     */
    function divide($a, $b, $roundingMode = self::ROUND_MODE_DEFAULT)
    {
        $result = bcdiv($a, $b, 1);
        return $this->round($result, $roundingMode);
    }

    /**
     * Rounds numbers that have a scale of 1 ONLY
     * @param string $number
     * @param int $roundingMode
     * @return string
     * @throws Exception\InvalidRoundingModeException
     */
    private function round($number, $roundingMode = self::ROUND_MODE_DEFAULT)
    {
        if( !in_array( $roundingMode, array( self::ROUND_MODE_HALF_EVEN, self::ROUND_MODE_HALF_ODD, self::ROUND_MODE_HALF_DOWN, self::ROUND_MODE_HALF_UP ) ) ) {
            throw new InvalidRoundingModeException("Invalid rounding mode '{$roundingMode}' provided");
        }

        if( strpos($number, '.' ) === false ) {
            return $number;
        }

        $preDecimal = substr($number, 0, strlen($number) - 2);
        $postDecimal = substr( $number, strlen($number) - 1 );
        if( '0' === $postDecimal ) {
            return bcdiv($number, 1, 0);
        }


        $isNegative = ( $preDecimal[0] === '-' );
        $isWholePartEven = ( bcmod($preDecimal, '2') === '0' );
        $addFunc = 'bcadd';
        $subFunc = 'bcsub';

        if( $isNegative ) {
            $addFunc = 'bcsub';
            $subFunc = 'bcadd';
        }

        if( $postDecimal ===  '5') {
            if( $roundingMode === self::ROUND_MODE_HALF_DOWN ) {
                return $subFunc($number, '0.5', 0);
            } elseif( $roundingMode === self::ROUND_MODE_HALF_UP ) {
                return $addFunc($number, '0.5', 0);
            } elseif( $roundingMode === self::ROUND_MODE_HALF_EVEN ) {
                if( $isWholePartEven ) {
                    return $addFunc($number, '0.5', 0);
                } else {
                    return $subFunc($number, '0.5', 0);
                }
            } else {
                if( $isWholePartEven ) {
                    return $subFunc($number, '0.5', 0);
                } else {
                    return $addFunc($number, '0.5', 0);
                }
            }
        } else {
            $postDecimal = (int) $postDecimal;
            if( $postDecimal < 5 ) {
                return bcdiv( $subFunc($number, '0.5', 0), '1' );
            } else {
                return bcdiv( $addFunc($number, '0.5', 0), '1' );
            }

        }
    }

}
