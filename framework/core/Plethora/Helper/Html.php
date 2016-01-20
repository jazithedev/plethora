<?php

namespace Plethora\Helper;

use Plethora\Helper;
use Plethora\Router;

/**
 * Helper for easy HTML tags generate.
 *
 * @package        Plethora
 * @subpackage     Helper
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Html extends Helper {
    /**
     * Factory method.
     *
     * @access     public
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function factory() {
        return new Html();
    }

    /**
     * Create anchor tag.
     *
     * @static
     * @access     public
     * @param      string     $sPath
     * @param      string     $sText
     * @param      Attributes $oAttributes
     * @return     string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function a($sPath, $sText, Attributes $oAttributes = NULL) {
        if($oAttributes === NULL) {
            $oAttributes = new Attributes();
        }

        $oAttributes->setAttribute('href', $sPath);

        return '<a '.$oAttributes->renderAttributes().'>'.$sText.'</a>';
    }

    /**
     * Create image tag.
     *
     * @static
     * @access     public
     * @param      string     $sImageSource
     * @param      string     $aAltText
     * @param      Attributes $oAttributes
     * @return     string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function img($sImageSource, $aAltText, Attributes $oAttributes = NULL) {
        if($oAttributes === NULL) {
            $oAttributes = new Attributes();
        }

        $oAttributes->setAttribute('src', $sImageSource);
        $oAttributes->setAttribute('alt', $aAltText);

        return '<img '.$oAttributes->renderAttributes().' />';
    }

    /**
     * Use the following method to remove white-spaces from the HTML code.
     *
     * @static
     * @access     public
     * @param      string $sString
     * @return     string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function minify($sString) {
        $aSearch = [
            '/\>[^\S ]+/s', // strip whitespaces after tags, except space
            '/[^\S ]+\</s', // strip whitespaces before tags, except space
            '/(\s)+/s'      // shorten multiple whitespace sequences
        ];

        $aReplace = [
            '>',
            '<',
            '\\1',
        ];

        $sSanitized = preg_replace($aSearch, $aReplace, $sString);

        return $sSanitized;
    }
}