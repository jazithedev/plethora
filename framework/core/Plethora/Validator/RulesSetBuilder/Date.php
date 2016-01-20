<?php

namespace Plethora\Validator\RulesSetBuilder;

use Plethora\Validator;

/**
 * Validator set builder for Date rules.
 *
 * @package        Plethora
 * @subpackage     Validator\RulesSetBuilder
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Date extends Validator\RulesSetBuilder {
    /**
     * Factory static method.
     *
     * @static
     * @access   public
     * @return   Date
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory() {
        return new Date();
    }

    /**
     * Checking if \Plethora\Form\Field\Date field is empty.
     *
     * @access   public
     * @param    array $aValues
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function notEmpty($aValues) {
        $this->addRule(['\Plethora\Validator\Rules\Date::notEmpty', [$aValues]]);

        return $this;
    }

    /**
     * Date validator for \Plethora\Form\Field\Date field.
     *
     * @access   public
     * @param    array $aValues
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function properDate(array $aValues) {

        $this->addRule(['\Plethora\Validator\Rules\Date::properDate', [$aValues]]);

        return $this;
    }
}