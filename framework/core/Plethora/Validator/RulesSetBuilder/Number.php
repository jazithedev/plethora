<?php

namespace Plethora\Validator\RulesSetBuilder;

use Plethora\Validator;

/**
 * Validator set builder for Number rules.
 *
 * @package        Plethora
 * @subpackage     Validator\RulesSetBuilder
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Number extends Validator\RulesSetBuilder {

    /**
     * Factory static method.
     *
     * @static
     * @access   public
     * @return   Number
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory() {
        return new Number();
    }

    /**
     * @access   public
     * @param    string $sValue
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function notEmpty($sValue) {
        $this->addRule(['\Plethora\Validator\Rules\Number::notEmpty', [$sValue]]);

        return $this;
    }

    /**
     * Check if particular value is a number.
     *
     * @access   public
     * @param    mixed $mValue
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function isNum($mValue) {
        $this->addRule(['\Plethora\Validator\Rules\Number::isNum', [$mValue]]);

        return $this;
    }

    /**
     * Checks if particular value is lower than some number.
     *
     * @access   public
     * @param    string  $mValue
     * @param    integer $iNum
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function min($mValue, $iNum) {
        $this->addRule(['\Plethora\Validator\Rules\Number::min', [$mValue, $iNum]]);

        return $this;
    }

    /**
     * Checks if particular value is higher than some number.
     *
     * @access   public
     * @param    string  $mValue
     * @param    integer $iNum
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function max($mValue, $iNum) {
        $this->addRule(['\Plethora\Validator\Rules\Number::max', [$mValue, $iNum]]);

        return $this;
    }

    /**
     * Check if particular value is unsigned.
     *
     * @access   public
     * @param    string $mValue
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function unsigned($mValue) {
        $this->addRule(['\Plethora\Validator\Rules\Number::unsigned', [$mValue]]);

        return $this;
    }

    /**
     * Check whether a value is an integer.
     *
     * @access   public
     * @param    string $mValue
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function integer($mValue) {
        $this->addRule(['\Plethora\Validator\Rules\Number::integer', [$mValue]]);

        return $this;
    }

    /**
     * Check whether a value is a float number.
     *
     * @access   public
     * @param    string  $mValue
     * @param    integer $iDecimal
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function float($mValue, $iDecimal = 2) {
        $this->addRule(['\Plethora\Validator\Rules\Number::float', [$mValue, $iDecimal]]);

        return $this;
    }

}