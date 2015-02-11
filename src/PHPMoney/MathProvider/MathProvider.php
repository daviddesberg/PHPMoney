<?php
namespace PHPMoney\MathProvider;

/**
 * @author David Desberg <david@daviddesberg.com>
 */

interface MathProvider
{
    /** Do not round */
    const ROUND_MODE_NONE = -2;
    /** Round halves up (4.5 --> 5) */
    const ROUND_MODE_HALF_UP = PHP_ROUND_HALF_UP;
    /** Round halves down (4.5 --> 4) */
    const ROUND_MODE_HALF_DOWN = PHP_ROUND_HALF_DOWN;
    /** Round halves up if the part of the number before the decimal is even. (4.5 --> 5, 5.5 --> 5, etc) */
    const ROUND_MODE_HALF_EVEN = PHP_ROUND_HALF_ODD; // PHP has these backwards according to my knowledge of the way these rounding mechanisms should work
    /** Round halves up if the part of the number before the decimal is odd. (4.5 --> 4, 5.5 --> 6, etc) */
    const ROUND_MODE_HALF_ODD = PHP_ROUND_HALF_EVEN;
    /** Default rounding mode, set to ROUND_MODE_HALF_EVEN as it is considered the preferred mechanism for financial calculations */
    const ROUND_MODE_DEFAULT = self::ROUND_MODE_HALF_EVEN;

    /**
     * @param $a string String representation of a value of the lowest unit of currency (i.e. cents). Must be whole number.
     * @param $b string String representation of cents. Must be whole number.
     * @return string String representation of a + b
     */
    function add($a, $b);

    /**
     * @param $a string String representation of a value of the lowest unit of currency (i.e. cents). Must be whole number.
     * @param $b string String representation of cents. Must be whole number.
     * @return string String representation of a - b
     */
    function subtract($a, $b);

    /**
     * @param $a string String representation of a value of the lowest unit of currency (i.e. cents). Must be whole number.
     * @param $b string String representation of multiplicand
     * @param $roundingMode int A rounding mode constant.
     * @return string String representation of a * b. Will be a whole number based upon rounding mode.
     */
    function multiply($a, $b, $roundingMode = self::ROUND_MODE_DEFAULT);

    /**
     * @param $a string String representation of a value of the lowest unit of currency (i.e. cents). Must be whole number.
     * @param $b string String representation of divisor
     * @param $roundingMode int A rounding mode constant.
     * @return string String representation of a / b. Will be a whole number based upon rounding mode
     */
    function divide($a, $b, $roundingMode = self::ROUND_MODE_DEFAULT);

    /**
     * @param $a string String representation of a numeric value.
     * @param $b string String representation of a numeric value.
     * @return int
     */
    function compare($a, $b);
}
