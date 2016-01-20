<?php

namespace Plethora\Validator\Rules;

use Plethora\ModelCore;

/**
 * Relations fields validation methods
 *
 * @package        Plethora
 * @subpackage     Validator\Rules
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Relation {
    /**
     * Check if particular relation field is not empty.
     *
     * @static
     * @access   public
     * @param    string $oValue
     * @return   boolean|string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function notEmpty($oValue) {
        if($oValue instanceof ModelCore) {
            return TRUE;
        }

        return __('Given value cannot be empty.');
    }
}