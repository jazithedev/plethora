<?php

namespace Plethora\Validator\Rules;

use Plethora\Exception;

/**
 * Date validator
 *
 * @package        Plethora
 * @subpackage     Validator\Rules
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Date {
    /**
     * Checking if \Plethora\Form\Field\Date field is empty.
     *
     * @static
     * @access   public
     * @param    array $aValues
     * @return   string|TRUE
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function notEmpty($aValues) {
        $aKeys = array_keys($aValues);

        if($aKeys == array_intersect($aKeys, ['month', 'day', 'year'])) {
            if($aValues['day'] !== '' && $aValues['month'] !== '' && $aValues['year'] !== '') {
                return TRUE;
            } else {
                return __('Given date cannot be empty.');
            }
        } else {
            return __('Given date is invalid.');
        }
    }

    /**
     * Date validator for \Plethora\Form\Field\Date field.
     *
     * @static
     * @access   public
     * @param    array $aValues
     * @return   string|TRUE
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function properDate(array $aValues) {
        $aKeys = array_keys($aValues);

        try {
            if($aKeys == array_intersect($aKeys, ['month', 'day', 'year'])) {
                if($aValues['day'] == '' && $aValues['month'] == '' && $aValues['year'] == '') {
                    return TRUE;
                }

                foreach($aValues as $sValue) {
                    if($sValue === '' || $sValue != (int)$sValue) {
                        throw new Exception();
                    }
                }

                $bCheck = checkdate($aValues['month'], $aValues['day'], $aValues['year']);

                return $bCheck ? TRUE : __('Given date is invalid.');
            } else {
                throw new Exception();
            }
        } catch(Exception $e) {
            return __('Invalid value.');
        }
    }
}