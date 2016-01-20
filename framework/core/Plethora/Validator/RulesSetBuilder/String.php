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
class String extends Validator\RulesSetBuilder {

    /** @noinspection PhpMissingParentCallCommonInspection */
    /**
     * Factory static method.
     *
     * @static
     * @access     public
     * @return     $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function factory() {
        return new String();
    }

    /**
     * @access     public
     * @param      string $sValue
     * @return     $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function notEmpty($sValue) {
        $this->addRule(['\Plethora\Validator\Rules\String::notEmpty', [$sValue]]);

        return $this;
    }

    /**
     * Checks whether particular value is an e-mail.
     *
     * @access     public
     * @param      string $sValue
     * @return     $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function email($sValue) {
        $this->addRule(['\Plethora\Validator\Rules\String::email', [$sValue]]);

        return $this;
    }

    /**
     * @access     public
     * @param      mixed   $mValue
     * @param      integer $iNum
     * @return     $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function min($mValue, $iNum) {
        $this->addRule(['\Plethora\Validator\Rules\String::min', [$mValue, $iNum]]);
        $this->addTip(__('Minimal length: :amount characters', ['amount' => $iNum]));

        return $this;
    }

    /**
     * @access     public
     * @param      mixed   $mValue
     * @param      integer $iNum
     * @return     $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function max($mValue, $iNum) {
        $this->addRule(['\Plethora\Validator\Rules\String::max', [$mValue, $iNum]]);
        $this->addTip(__('Maximal length: :amount characters', ['amount' => $iNum]));

        return $this;
    }

    /**
     * @access     public
     * @param      string $sValue
     * @param      string $sCompareTo
     * @return     $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function like($sValue, $sCompareTo) {
        $this->addRule(['\Plethora\Validator\Rules\String::like', [$sValue, $sCompareTo]]);

        return $this;
    }

    /**
     * Checks if particular string contains any numeric characters.
     *
     * @access     public
     * @param      string $sValue
     * @return     $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function containNumbers($sValue) {
        $this->addRule(['\Plethora\Validator\Rules\String::containNumbers', [$sValue]]);

        return $this;
    }

    /**
     * Checks if particular string contains any text characters.
     *
     * @access     public
     * @param      string $sValue
     * @return     $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function containText($sValue) {
        $this->addRule(['\Plethora\Validator\Rules\String::containText', [$sValue]]);

        return $this;
    }

    /**
     * Checks if particular string contains any uppercase characters.
     *
     * @access     public
     * @param      string $sValue
     * @return     $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function containUppercase($sValue) {
        $this->addRule(['\Plethora\Validator\Rules\String::containUppercase', [$sValue]]);

        return $this;
    }

    /**
     * Checks if particular string contains any custom characters.
     *
     * @access     public
     * @param      string $sValue
     * @param      string $sChars
     * @return     $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function containCustomCharacters($sValue, $sChars = ',.\]\[_\-<>\/\+=!@#$%^&*()|;') {
        $this->addRule(['\Plethora\Validator\Rules\String::containCustomCharacters', [$sValue, $sChars]]);

        return $this;
    }

    /**
     * Checks if particular value contain letters only.
     *
     * @access     public
     * @param      string $mValue
     * @return     $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function onlyLetters($mValue) {
        $this->addRule(['\Plethora\Validator\Rules\String::onlyLetters', [$mValue]]);
        $this->addTip(__('The value can contain only letters.'));

        return $this;
    }

    /**
     * Checks whether particular value contain only letters and some of special characters.
     *
     * @access     public
     * @param      mixed  $mValue
     * @param      string $sChars
     * @param      string $sErrorMsg
     * @return     $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function onlyLettersAndChars($mValue, $sChars, $sErrorMsg = '') {
        $this->addRule(['\Plethora\Validator\Rules\String::onlyLettersAndChars', [$mValue, $sChars, $sErrorMsg]]);

        return $this;
    }

    /**
     * Checks whether particular value contain only letters, numbers and some of special characters.
     *
     * @access     public
     * @param      mixed  $mValue
     * @param      string $sChars
     * @param      string $sErrorMsg
     * @return     $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function onlyLettersNumsAndChars($mValue, $sChars, $sErrorMsg = '') {
        $this->addRule(['\Plethora\Validator\Rules\String::onlyLettersNumsAndChars', [$mValue, $sChars, $sErrorMsg]]);

        return $this;
    }

    /**
     * Checks whether particular string is an URL.
     *
     * @access     public
     * @param      string $mValue
     * @return     $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function url($mValue) {
        $this->addRule(['\Plethora\Validator\Rules\String::url', [$mValue]]);

        return $this;
    }

    /**
     * Check if particular string is properly validated for the specified regex
     * value.
     *
     * @access     public
     * @param      string $sValue
     * @param      string $sRegex
     * @param      string $sTip
     * @return     $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function regex($sValue, $sRegex, $sTip = NULL) {
        $this->addRule(['\Plethora\Validator\Rules\String::regex', [$sValue, $sRegex]]);

        if($sTip === NULL) {
            $this->addTip(__('Regex expression restrictions: :regex.', ['regex' => $sRegex]));
        } else {
            $this->addTip($sTip);
        }

        return $this;
    }

}
