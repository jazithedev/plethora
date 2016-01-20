<?php

namespace Plethora\Helper;

use Plethora\Helper;

/**
 * Helper for JSON content operations.
 *
 * @package        Plethora\Helper
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Json extends Helper
{
    /**
     * Factory method.
     *
     * @static
     * @access   public
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory()
    {
        return new Json;
    }

    /**
     * Serialize array.
     *
     * @static
     * @access   public
     * @param    array $aArray
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function serialize(array $aArray)
    {
        return json_encode($aArray);
    }

    /**
     * Unserialize data.
     *
     * @static
     * @access   public
     * @param    string $sJson
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function unserialize($sJson)
    {
        return Arrays::get(json_decode($sJson, TRUE), 'response');
    }

    /**
     * Return value of JSON decoder error.
     *
     * @static
     * @access   public
     * @param    string$id
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function returnJsonError($id)
    {
        switch($id) {
            case JSON_ERROR_NONE:
                return 'No errors';
            case JSON_ERROR_DEPTH:
                return 'Maximum stack depth exceeded';
            case JSON_ERROR_STATE_MISMATCH:
                return 'Underflow or the modes mismatch';
            case JSON_ERROR_CTRL_CHAR:
                return 'Unexpected control character found';
            case JSON_ERROR_SYNTAX:
                return 'Syntax error, malformed JSON';
            case JSON_ERROR_UTF8:
                return 'Malformed UTF-8 characters, possibly incorrectly encoded';
            default:
                return 'Unknown error';
        }
    }
}