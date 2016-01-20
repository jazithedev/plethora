<?php

namespace Plethora\Validator\RulesSetBuilder;

use Plethora\Validator;

/**
 * Validator set builder for Regex rules.
 *
 * @package        Plethora
 * @subpackage     Validator\RulesSetBuilder
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Regex extends Validator\RulesSetBuilder {

    /**
     * Factory static method.
     *
     * @static
     * @access   public
     * @return   Regex
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory() {
        return new Regex();
    }

    /**
     * Check if particular value meet the requirements from the regex formula.
     *
     * @access   public
     * @param    mixed  $mValue
     * @param    string $sFormula
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function regex($mValue, $sFormula) {
        $this->addRule(['\Plethora\Validator\Rules\Regex::regex', [$mValue, $sFormula]]);

        return $this;
    }

}