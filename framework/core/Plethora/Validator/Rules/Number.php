<?php

namespace Plethora\Validator\Rules;

use Plethora\Exception;

/**
 * Number validation methods
 *
 * @package        Plethora
 * @subpackage     Validator\Rules
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Number {

    /**
     * @static
     * @access   public
     * @param    string $sValue
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function notEmpty($sValue) {
        return ($sValue != 0 && $sValue !== NULL && $sValue !== FALSE) ? TRUE : __('Given value cannot be empty.');
    }

    /**
     * Check if particular value is a number.
     *
     * @static
     * @access   public
     * @param    mixed $mValue
     * @return   boolean|string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function isNum($mValue) {
        if(!is_numeric($mValue)) {
            return __('Invalid value - it\'s not a number.');
        }

        return TRUE;
    }

    /**
     * Checks if particular value is lower than some number.
     *
     * @static
     * @access   public
     * @param    string  $mValue
     * @param    integer $iNum
     * @return   boolean|string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function min($mValue, $iNum) {
        if($mValue < $iNum) {
            return __('Given value is lower than :num.', ['num' => $iNum]);
        }

        return TRUE;
    }

    /**
     * Checks if particular value is higher than some number.
     *
     * @static
     * @access   public
     * @param    string  $mValue
     * @param    integer $iNum
     * @return   boolean|string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function max($mValue, $iNum) {
        if($mValue > $iNum) {
            return __('Given value is higher than :num.', ['num' => $iNum]);
        }

        return TRUE;
    }

    /**
     * Check if particular value is unsigned.
     *
     * @static
     * @access   public
     * @param    string $mValue
     * @return   boolean|string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function unsigned($mValue) {
        if($mValue < 0) {
            return __('This value must be an unsigned number.');
        }

        return TRUE;
    }

    /**
     * Check whether a value is an integer.
     *
     * @static
     * @access   public
     * @param    string $mValue
     * @return   boolean|string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function integer($mValue) {
        if((int)$mValue != $mValue) {
            return __('This value must be an integer.');
        }

        return TRUE;
    }

    /**
     * Check whether a value is a float number.
     *
     * @static
     * @access   public
     * @param    string  $mValue
     * @param    integer $iDecimal
     * @return   bool|string
     * @throws   Exception\Fatal
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function float($mValue, $iDecimal = 2) {
        if(!is_int($iDecimal)) {
            throw new Exception\Fatal('Second argument of this method should be an integer.');
        }

        if(!preg_match('/\A[0-9]+[.]{0,1}[0-9]{0,'.$iDecimal.'}\Z/', $mValue)) {
            return __('Particular value should be a float with :decimal places after the decimal point.', ['decimal' => $iDecimal]);
        }

        return TRUE;
    }

}
