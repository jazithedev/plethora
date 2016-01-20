<?php

namespace Plethora\Validator\Rules;

/**
 * Strings validation methods
 *
 * @package        Plethora
 * @subpackage     Validator\Rules
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class String {

    /**
     * Check if string is not empty.
     *
     * @static
     * @access   public
     * @param    string $sValue
     * @return   boolean|string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function notEmpty($sValue) {
        if(!is_string($sValue)) {
            return __('Given value is not a string.');
        }

        return ($sValue !== '' && $sValue !== NULL && $sValue !== FALSE) ? TRUE : __('Given value cannot be empty.');
    }

    /**
     * Checks if particular value is an e-mail.
     *
     * @static
     * @access   public
     * @param    string $sValue
     * @return   boolean|string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function email($sValue) {
        if(!preg_match('/\A\b[a-z0-9._%-]+@[a-z0-9.-]+\.[a-z]{2,4}\b\Z/', $sValue)) {
            return __('Given e-mail address is invalid.');
        }

        return TRUE;
    }

    /**
     * Check whether the string has required, minimal amount of characters.
     *
     * @static
     * @access     public
     * @param    string  $mValue
     * @param    integer $iNum
     * @return    boolean|string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function min($mValue, $iNum) {
        if(mb_strlen($mValue, 'UTF-8') < $iNum) {
            return __('The amount of this string characters cannot be lower than :num.', ['num' => $iNum]);
        }

        return TRUE;
    }

    /**
     * Check whether the string does not have amount of characters beyond the particular limit.
     *
     * @static
     * @access     public
     * @param    string  $mValue
     * @param    integer $iNum
     * @return    boolean|string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function max($mValue, $iNum) {
        if(mb_strlen($mValue, 'UTF-8') > $iNum) {
            return __('The amount of this string characters cannot be greater than :num.', ['num' => $iNum]);
        }

        return TRUE;
    }

    /**
     * Compare one string to another.
     *
     * @static
     * @access     public
     * @param    string $mValue
     * @param    string $sCompareTo
     * @return    boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function like($mValue, $sCompareTo) {
        return ($mValue === $sCompareTo) ? TRUE : __('Value of this and previous field must be the same.');
    }

    /**
     * Checks if particular string contains any numeric characters.
     *
     * @static
     * @access     public
     * @param    string|array $mValue
     * @return    boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function containNumbers($mValue) {
        return preg_match('/[0-9]+/', $mValue) ? TRUE : __('This value must contain at least one number.');
    }

    /**
     * Checks if particular string contains any text characters.
     *
     * @static
     * @access     public
     * @param    string|array $mValue
     * @return    boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function containText($mValue) {
        return preg_match('/[a-zółńćźżąśę]+/', $mValue) ? TRUE : __('This value must contain at least one text character.');
    }

    /**
     * Checks if particular string contains any uppercase characters.
     *
     * @static
     * @access     public
     * @param    string|array $mValue
     * @return    boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function containUppercase($mValue) {
        return preg_match('/[A-ZÓŁĆŹŻĘĄŚŃ]+/', $mValue) ? TRUE : __('This value must contain at least one uppercase text character.');
    }

    /**
     * Checks if particular string contains any custom characters.
     *
     * @static
     * @access     public
     * @param    string $mValue
     * @param    string $sChars
     * @return    boolean|string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function containCustomCharacters($mValue, $sChars = ',.\]\[_\-<>\/\+=!@#$%^&*()|;') {
        $sCharsToMsg = str_replace(['\\\\', '\\', 'tochange'], ['tochange', '', '\\'], $sChars);

        return preg_match('/['.$sChars.']+/', $mValue) ? TRUE : __('This value must contain at least one custom character (one from these: :chars).', ['chars' => $sCharsToMsg]);
    }

    /**
     * Checks if particular value contain letters only.
     *
     * @static
     * @access     public
     * @param    mixed $mValue
     * @return    boolean|string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function onlyLetters($mValue) {
        return preg_match('/^[a-zA-ZółńćźżąśęÓŁĆŹŻĘĄŚŃ]+$/', $mValue) ? TRUE : __('This value must contain letters only.');
    }

    /**
     * Checks whether particular value contain only letters and some of special characters.
     *
     * @static
     * @access     public
     * @param      mixed  $mValue
     * @param      string $sChars
     * @param      string $sErrorMsg
     * @return     bool|string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function onlyLettersAndChars($mValue, $sChars, $sErrorMsg = '') {
        if($sErrorMsg === '') {
            $sCharsToMsg = str_replace(['\\\\', '\\', 'tochange'], ['tochange', '', '\\'], $sChars);

            $sErrorMsg = __('This value should contain only letters and some of the characters (:chars).', ['chars' => $sCharsToMsg]);
        }

        return preg_match('/^[a-zA-ZółńćźżąśęÓŁĆŹŻĘĄŚŃ'.$sChars.']+$/', $mValue) ? TRUE : $sErrorMsg;
    }

    /**
     * Checks whether particular value contain only letters, numbers and some of special characters.
     *
     * @static
     * @access   public
     * @param    mixed  $mValue
     * @param    string $sChars
     * @param    string $sErrorMsg
     * @return   bool|string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function onlyLettersNumsAndChars($mValue, $sChars, $sErrorMsg = '') {
        if($sErrorMsg === '') {
            $sCharsToMsg = str_replace(['\\\\', '\\', 'tochange'], ['tochange', '', '\\'], $sChars);

            $sErrorMsg = __('This value should contain letters, numbers and some of the characters (:chars).', ['chars' => $sCharsToMsg]);
        }

        return preg_match('/^[a-zA-Z0-9ółńćźżąśęÓŁĆŹŻĘĄŚŃ'.$sChars.']+$/', $mValue) ? TRUE : $sErrorMsg;
    }

    /**
     * Checks whether particular string is an URL.
     *
     * @static
     * @access   public
     * @param    string $mValue
     * @return   boolean|string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function url($mValue) {
        $sFormula = "%\A(?:^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,})))(?::\d{2,5})?(?:/[^\s]*)?$)\Z%u";

        if(!preg_match($sFormula, $mValue) && !empty($mValue)) {
            return __('Particular value is not an URL.');
        }

        return TRUE;
    }

    /**
     * Check if particular string is properly validated for the specified regex
     * value.
     *
     * @static
     * @access   public
     * @param    string $sValue
     * @param    string $sRegex
     * @return   boolean|string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function regex($sValue, $sRegex) {
        $aOutput = [];

        preg_match('/'.$sRegex.'/', $sValue, $aOutput);

        if(empty($aOutput)) {
            return __('Value is invalid for this regular expression: :exp', ['exp' => $sRegex]);
        } else {
            return TRUE;
        }
    }
}