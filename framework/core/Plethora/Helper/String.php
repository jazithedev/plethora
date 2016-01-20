<?php

namespace Plethora\Helper;

use Plethora\Helper;

/**
 * @package        Plethora
 * @subpackage     Form\Separator
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class String extends Helper {

    /**
     * Prepare given value for using in URL
     *
     * @static
     * @access   public
     * @param    string $sInput
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function prepareToURL($sInput) {
        $aChars = [
            'ą'     => 'a',
            'Ą'     => 'A',
            'ć'     => 'c',
            'Ć'     => 'C',
            'ż'     => 'z',
            'Ż'     => 'Z',
            'ź'     => 'z',
            'Ź'     => 'Z',
            'ę'     => 'e',
            'Ę'     => 'E',
            'ś'     => 's',
            'Ś'     => 'S',
            'ń'     => 'n',
            'Ń'     => 'N',
            'ó'     => 'o',
            'Ó'     => 'O',
            'ł'     => 'l',
            'Ł'     => 'L',
            '?'     => '',
            "\\"    => '_',
            '/'     => '%2F',
            ' '     => '_',
            '.'     => '',
            '('     => '',
            ','     => '_',
            ')'     => '',
            '*'     => '',
            '"'     => '',
            '<br>'  => '_',
            '___'   => '_',
            '__'    => '_',
            '%'     => '%25',
            '\''    => '',
            '„'     => '',
            '”'     => '',
            '“'     => '',
            '–'     => '-',
            '!'     => '',
            '#'     => '',
            '&amp;' => 'and',
            '&'     => 'and',
            'quot;' => '',
            '®'     => '(R)',
            ':'     => '%3A',
        ];

        foreach($aChars as $sBefore => $sAfter) {
            $sInput = str_ireplace($sBefore, $sAfter, $sInput);
        }

        $sValueTmp = strip_tags($sInput);
        $sOutput   = strtolower(trim(rtrim($sValueTmp, '_')));

        return $sOutput;
    }

    /**
     * Prepare given value for using in attributes of HTML tags. For example, in "alt", "title".
     *
     * @static
     * @access   public
     * @param    string $sInput
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function prepareToCode($sInput) {
        $sValue = trim($sInput);

        $aChars = [
            '"' => '\'', '&' => '&amp;', '\'' => '&#39;',
        ];

        foreach($aChars as $sBefore => $sAfter) {
            $sValue = str_ireplace($sBefore, $sAfter, $sValue);
        }

        $sValueTmp = strip_tags($sValue);
        $sOutput   = trim(rtrim($sValueTmp, '_'));

        return $sOutput;
    }

    /**
     * Cut string to appropriate length without cutting words in a pieces. Ends string with a replacer (if longer than
     * expected).
     *
     * @static
     * @access   public
     * @param    string  $sInput
     * @param    integer $iLength
     * @param    string  $sReplacer
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function substrWords($sInput, $iLength, $sReplacer = '...') {
        $sStrippedInput = strip_tags($sInput);

        if(strlen($sStrippedInput) > $iLength) {
            $iLastWhiteSpacePos = strripos(substr($sStrippedInput, 0, $iLength), 32);

            return substr_replace($sStrippedInput, $sReplacer, $iLastWhiteSpacePos);
        } else {
            return $sStrippedInput;
        }
    }

    /**
     * Return placeholder if value is empty.
     *
     * @static
     * @access   public
     * @param    string $sInput
     * @param    string $sPlaceholder
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function placeholder($sInput, $sPlaceholder) {
        return empty($sInput) ? $sPlaceholder : $sInput;
    }

    /**
     * Rewrite string from camel case to underscore.
     *
     * @static
     * @access   public
     * @param    string $sInput
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function decamelize($sInput) {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $sInput, $aMatches);
        $aRet = $aMatches[0];

        foreach($aRet as &$sMatch) {
            $sMatch = $sMatch == strtoupper($sMatch) ? strtolower($sMatch) : lcfirst($sMatch);
        }

        return implode('_', $aRet);
    }

    /**
     * Rewrite string from underscore to camel case.
     *
     * @static
     * @access   public
     * @param    string $sInput
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function camelize($sInput) {
        return preg_replace_callback('/(^|_)([a-z])/', create_function('$matches', 'return strtoupper($matches[2]);'), $sInput);
    }

}
