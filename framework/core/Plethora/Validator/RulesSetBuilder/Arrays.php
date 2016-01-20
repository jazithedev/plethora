<?php

namespace Plethora\Validator\RulesSetBuilder;

use Plethora\Validator;

/**
 * @package        Plethora
 * @subpackage     Validator\RulesSetBuilder
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Arrays extends Validator\RulesSetBuilder {
    /**
     * Factory static method.
     *
     * @static
     * @access   public
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory() {
        return new Arrays();
    }

    /**
     * Check if array is not empty.
     *
     * @access   public
     * @param    array $value
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function notEmpty($value) {
        $this->addRule(['\Plethora\Validator\Rules\Arrays::notEmpty', [$value]]);

        return $this;
    }

    /**
     * Check whether a particular value is one of the other values.
     *
     * @access   public
     * @param    array $value
     * @param    array $compare
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function inArray($value, array $compare) {
        $this->addRule(['\Plethora\Validator\Rules\Arrays::inArray', [$value, $compare]]);

        return $this;
    }
}