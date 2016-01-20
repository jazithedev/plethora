<?php

namespace Plethora\Validator;

/**
 * Number validation methods
 *
 * @package        Plethora\Validator
 * @subpackage     Rules
 * @author         Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Rules {
    /**
     * Is required?
     *
     * @static
     * @access   public
     * @param    string $mValue
     * @return   boolean|string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function required($mValue) {
        if(!is_array($mValue) && ($mValue === NULL || $mValue === '')) {
            return __('Given value is empty.');
        } elseif(is_array($mValue)) {
            if(!count($mValue)) {
                return __('The amount of values must be higher than 0.');
            } else {
                foreach($mValue as $v) {
                    if($v === NULL || $v === '') {
                        return __('One of the values is empty.');
                    }
                }
            }
        }

        return TRUE;
    }

    /**
     * Check if particular value is same like the value from the given field.
     *
     * @static
     * @access   public
     * @param    mixed  $mValue
     * @param    string $sOtherFieldValue
     * @return   bool|string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function sameAs($mValue, $sOtherFieldValue) {
        return ($mValue === $sOtherFieldValue) ? TRUE : __('The value of this field must be the same as the value of the previous field.');
    }
}