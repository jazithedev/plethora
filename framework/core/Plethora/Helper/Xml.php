<?php

namespace Plethora\Helper;

use Plethora\Helper;

/**
 * XML helper
 *
 * @package        Plethora
 * @subpackage     Form\Separator
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Xml extends Helper {
    /**
     * Changes XML string to an array.
     *
     * @static
     * @access   public
     * @param    string $sXML
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function toArray($sXML) {
        $array = json_decode(json_encode($sXML), TRUE);

        foreach(array_slice($array, 0) as $key => $value) {
            if(empty($value)) {
                $array[$key] = NULL;
            } elseif(is_array($value)) {
                $array[$key] = self::toArray($value);
            }
        }

        return $array;
    }
}

?>
