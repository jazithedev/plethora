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
class Relation extends Validator\RulesSetBuilder {
    /**
     * Factory static method.
     *
     * @static
     * @access   public
     * @return   Relation
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory() {
        return new Relation();
    }

    /**
     * Check if particular relation field is not empty.
     *
     * @access   public
     * @param    string $sValue
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function notEmpty($sValue) {
        $this->addRule(['\Plethora\Validator\Rules\Relation::notEmpty', [$sValue]]);

        return $this;
    }
}