<?php
namespace PHPMoney\MathProvider;
use PHPMoney\MathProvider\Exception\InvalidRoundingModeException;

/**
 * @author David Desberg <david@daviddesberg.com>
 */
class BCMathProvider implements MathProvider
{
    /** Scale for multiplication and division */
    const MULTIPLICATION_DIVISION_SCALE = 5;

    /**
     * @param $a string String representation of a whole numeric value.
     * @param $b string String representation of a whole numeric value.
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
        $result = bcmul($a, $b, self::MULTIPLICATION_DIVISION_SCALE);
        if( self::ROUND_MODE_NONE === $roundingMode ) {
            return rtrim($result, '0');
        }
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
        $result = bcdiv($a, $b, self::MULTIPLICATION_DIVISION_SCALE);
        if( self::ROUND_MODE_NONE === $roundingMode ) {
            return rtrim($result, '0');
        }
        return $this->round($result, $roundingMode);
    }

    /**
     * Rounds the results of the multiply/divide methods to whole numbers (since they are to be used as currency values)
     * @param string $number
     * @param int $roundingMode
     * @return string
     * @throws Exception\InvalidRoundingModeException
     */
    public function round($number, $roundingMode = self::ROUND_MODE_DEFAULT)
    {
        if( !in_array( $roundingMode, array( self::ROUND_MODE_HALF_EVEN, self::ROUND_MODE_HALF_ODD, self::ROUND_MODE_HALF_DOWN, self::ROUND_MODE_HALF_UP ) ) ) {
            throw new InvalidRoundingModeException("Invalid rounding mode '{$roundingMode}' provided");
        }

        $decPosition = strpos($number, '.' );
        if( $decPosition === false ) {
            return $number;
        }

        $preDecimal = substr($number, 0, $decPosition );
        $postDecimal = substr( $number, $decPosition + 1 );

        if( trim($postDecimal, '0') === '' ) {
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


        if( rtrim($postDecimal, '0') ===  '5') {
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
            $fiveOperand = '5' . ( strlen($postDecimal) - 2 > 0 ) ?: str_pad('0', strlen($postDecimal) - 2);
            if( $postDecimal[0] !== '0' && (int) $postDecimal <= (int) $fiveOperand ) { // if postDecimal == 5, it'll be handled up there
                return bcdiv( $subFunc($number, '0.1', 0), '1' );
            } else {
                return bcdiv( $addFunc($number, '0.5', 0), '1' );
            }

        }
    }

}
