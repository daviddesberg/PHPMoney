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
        if (!is_int($value) && !is_string($value)) {
            throw new InvalidArgumentException('Invalid $value type, expected int|string but got ' . gettype($value));
        }

        $this->value = (string) $value;
        $this->mathProvider = $mathProvider;
    }

    /**
     * Formats the value for output
     * e.g. if value is 100000, will output either 1,000.00 if commas is set to true and decimalPlaces is set to 2
     * @param string $thousandsSeparator Separator for thousands.
     * @param int $decimalPlaces Number spots the decimal places is towards the left of the number from its end.
     * @param string $decimalPoint Separator for the decimal point.
     * @return string
     */
    public function format($thousandsSeparator = ',', $decimalPlaces = 2, $decimalPoint = '.')
    {
        $value = $this->getValue();

        if ($decimalPlaces > 0) {
            if (strlen($value) > $decimalPlaces) {
                $value = substr($value, 0, strlen($value) - $decimalPlaces) . $decimalPoint . substr($value, strlen($value) - $decimalPlaces);
            } else {
                $difference = $decimalPlaces - strlen($value);
                $value = $decimalPoint . str_pad('0', $difference) . $value;
            }
        }

        $value = preg_replace('/(?<=\\d)(?=(\\d{3})+(?!\\d))/', $thousandsSeparator, $value);

        if ($value[0] === $decimalPoint) {
            $value = '0' . $value;
        }

        return $value;
    }

    /**
     * Adds $money to $this and returns a new value object.
     * @param Money $money
     * @return Money
     */
    public function add(Money $money)
    {
        return new static( $this->mathProvider->add( $this->getValue(), $money->getValue() ), $this->mathProvider );
    }

    /**
     * Subtracts $money from $this and returns a new value object.
     * @param Money $money
     * @return Money
     */
    public function subtract(Money $money)
    {
        return new static( $this->mathProvider->subtract( $this->getValue(), $money->getValue() ), $this->mathProvider );
    }

    /**
     * Divides $this by $divisor and returns a new value object.
     * Uses the default rounding method (ROUND_HALF_EVEN) which is preferred for financial calculations.
     * @param Money|int|float|string $divisor
     * @return Money|string
     */
    public function divide($divisor)
    {
        if ($divisor instanceof Money) {
            return $this->mathProvider->divide( $this->getValue(), $divisor->getValue(), MathProvider::ROUND_MODE_NONE );
        } else {
            return new static( $this->mathProvider->divide( $this->getValue(), (string) $divisor ), $this->mathProvider );
        }
    }

    /**
     * Multiplies $this by $multiplicand and returns a new value object.
     * Uses the default rounding method (ROUND_HALF_EVEN) which is preferred for financial calculations.
     * @throws InvalidMultiplicandException
     * @param int|float|string $multiplicand
     * @return Money
     */
    public function multiply($multiplicand)
    {
        if (!is_scalar($multiplicand)) {
            throw new InvalidMultiplicandException('Invalid multiplicand of type ' . gettype($multiplicand) . ' passed to Money::multiply');
        }
        return new static( $this->mathProvider->multiply( $this->getValue(), (string) $multiplicand ), $this->mathProvider );
    }

    /**
     * Returns a new Money object that contains the absolute value of $this
     */
    public function abs()
    {
        if ($this->lessThan(0)) {
            return $this->multiply('-1');
        } else {
            return $this->multiply('1');
        }
    }

    /**
     * Returns whether or not the value held by $this is the same as the value held by $money
     * @param Money|string|int $money
     * @return bool
     */
    public function equals($money)
    {
        if ($money instanceof Money) {
            $money = $money->getValue();
        }
        return $this->mathProvider->compare( $this->getValue(), (string) $money ) === 0;
    }

    /**
     * Returns whether or not the value held by $this is less than the value held by $money
     * @param Money|string|int $money
     * @return bool
     */
    public function lessThan($money)
    {
        if ($money instanceof Money) {
            $money = $money->getValue();
        }
        return $this->mathProvider->compare( $this->getValue(), (string) $money ) === -1;
    }

    /**
     * Returns whether or not the value held by $this is greater than the value held by $money
     * @param Money|string|int $money
     * @return bool
     */
    public function greaterThan($money)
    {
        if ($money instanceof Money) {
            $money = $money->getValue();
        }
        return $this->mathProvider->compare( $this->getValue(), (string) $money ) === 1;
    }

    /**
     * @return string Value of object
     */
    public function __toString()
    {
        return $this->getValue();
    }

    /**
     * @return string Value of object
     */
    public function getValue()
    {
        return $this->value;
    }
}
