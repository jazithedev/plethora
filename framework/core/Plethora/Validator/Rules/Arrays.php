<?php

namespace Plethora\Validator\Rules;

/**
 * Validator for data with arrays
 *
 * @package        Plethora
 * @subpackage     Validator\Rules
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Arrays {

    /**
     * Check if array is not empty.
     *
     * @static
     * @access   public
     * @param    array $aValue
     * @return   boolean|string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function notEmpty($aValue) {
        if(!is_array($aValue)) {
            return __('Given value is not a string.');
        }

        return ($aValue !== []) ? TRUE : __('At least one value for this field must be picked.');
    }

    /**
     * Check whether a particular value is one of the other values.
     *
     * @static
     * @access   public
     * @param    array $aValue
     * @param    array $aCompare
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function inArray(array $aValue, array $aCompare) {
        return (array_diff($aCompare, $aValue) !== $aCompare) ? TRUE : __('Invalid value.');
    }

}
