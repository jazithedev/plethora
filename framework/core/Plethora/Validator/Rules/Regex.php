<?php

namespace Plethora\Validator\Rules;

/**
 * Validation regex rules.
 *
 * @package        Plethora
 * @subpackage     Validator\Rules
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Regex {

    /**
     * Check if particular value meet the requirements from the regex formula.
     *
     * @static
     * @access   public
     * @param    mixed  $mValue
     * @param    string $sFormula
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function regex($mValue, $sFormula) {
        if(!preg_match($sFormula, $mValue)) {
            return __('The specified value does not meet the requirements for specified regular expression formula: :regex', ['regex' => $sFormula]);
        }

        return TRUE;
    }

}