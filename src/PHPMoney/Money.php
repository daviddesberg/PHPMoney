<?php
namespace PHPMoney;
use PHPMoney\Exception\InvalidArgumentException;
use PHPMoney\Exception\InvalidMultiplicandException;
use PHPMoney\MathProvider\MathProvider;

/**
 * @author David Desberg <david@daviddesberg.com>
 */
class Money
{
    private $value;
    private $mathProvider;

    /**
     * Constructs a new money instance.
     * @param $value string|int Representation of the money value in the lowest denomination of that currency (for USD, cents. pence for GBP, etc.)
     * @param $mathProvider MathProvider
     * @throws InvalidArgumentException
     */
    public function __construct($value, MathProvider $mathProvider)
    {
        if( !is_int($value) && !is_string($value) ) {
            throw new InvalidArgumentException('Invalid $value type, expected int|string but got ' . gettype($value));
        }

        $this->value = (string) $value;
        $this->mathProvider = $mathProvider;
    }

    /**
     * Formats the value for output
     * e.g. if value is 100000, will output either 1,000.00 if commas is set to true and decimalPlaces is set to 2
     * @param bool $commas
     * @param int $decimalPlaces
     * @return string
     */
    public function format($commas = true, $decimalPlaces = 2)
    {
        $value = $this->value;

        if( $decimalPlaces > 0 ) {
            if( strlen($value) > $decimalPlaces ) {
                $value = substr($value, 0, strlen($value) - $decimalPlaces) . '.' . substr($value, strlen($value) - $decimalPlaces);
            } else {
                $difference = $decimalPlaces - strlen($value);
                $value = '.' . str_pad('0', $difference) . $value;
            }
        }

        if( $commas ) {
            $value = preg_replace('/(?<=\\d)(?=(\\d{3})+(?!\\d))/', ',', $value);
        }

        if( $value[0] === '.' ) {
            $value = '0' . $value;
        }

        return $value;
    }

    /**
     * @return string Value of object
     */
    public function __toString()
    {
        return $this->value;
    }

    /**
     * Adds $money to $this and returns a new value object.
     * @param Money $money
     * @return static
     */
    public function add(Money $money)
    {
        return new static( $this->mathProvider->add( (string) $this, (string) $money ), $this->mathProvider );
    }

    /**
     * Subtracts $money from $this and returns a new value object.
     * @param Money $money
     * @return static
     */
    public function subtract(Money $money)
    {
        return new static( $this->mathProvider->subtract( (string) $this, (string) $money ), $this->mathProvider );
    }

    /**
     * Divides $this by $divisor and returns a new value object.
     * Uses the default rounding method (ROUND_HALF_EVEN) which is preferred for financial calculations.
     * @param Money|int|float|string $divisor
     * @return static|string
     */
    public function divide($divisor)
    {
        if( is_scalar($divisor) ) {
            return new static( $this->mathProvider->divide( (string) $this, (string) $divisor ), $this->mathProvider );
        } else {
            return $this->mathProvider->divide( ( string ) $this, ( string ) $divisor, MathProvider::ROUND_MODE_NONE );
        }
    }

    /**
     * Multiplies $this by $multiplicand and returns a new value object.
     * Uses the default rounding method (ROUND_HALF_EVEN) which is preferred for financial calculations.
     * @throws InvalidMultiplicandException
     * @param Money|int|float|string $multiplicand
     * @return static
     */
    public function multiply($multiplicand)
    {
        if( !is_scalar($multiplicand) ) {
            throw new InvalidMultiplicandException('Invalid multiplicand of type ' . gettype($multiplicand) . ' passed to Money::multiply');
        }
        return new static( $this->mathProvider->multiply( (string) $this, (string) $multiplicand ), $this->mathProvider );
    }

    /**
     * Returns whether or not the value held by $this is the same as the value held by $money
     * @param Money $money
     * @return bool
     */
    public function equals(Money $money)
    {
        return $this->mathProvider->compare( ( string ) $this, ( string ) $money ) === 0;
    }

    /**
     * Returns whether or not the value held by $this is less than the value held by $money
     * @param Money $money
     * @return bool
     */
    public function lessThan(Money $money)
    {
        return $this->mathProvider->compare( ( string ) $this, ( string ) $money ) === -1;
    }

    /**
     * Returns whether or not the value held by $this is greater than the value held by $money
     * @param Money $money
     * @return bool
     */
    public function greaterThan(Money $money)
    {
        return $this->mathProvider->compare( ( string ) $this, ( string ) $money ) === 1;
    }
}