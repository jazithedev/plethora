<?php

namespace Plethora\Helper;

use Plethora\Helper;
use Plethora\Log;

/**
 * @package        Plethora
 * @subpackage     Form\Separator
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class PolishMonths extends Helper {
    /**
     * Constructor
     *
     * @access   public
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __construct() {
        Log::insert('MonthHelper object initialized!');
    }

    /**
     * Turns string version of month into integer version.
     *
     * @access   public
     * @param    string  $str
     * @param    boolean $leadingZeros
     * @return   integer
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function strToInt($str, $leadingZeros = FALSE) {
        if(in_array($str, ['styczeń', 'stycznia'])) {
            $int = 1;
        } elseif(in_array($str, ['luty', 'lutego'])) {
            $int = 2;
        } elseif(in_array($str, ['marzec', 'marca'])) {
            $int = 3;
        } elseif(in_array($str, ['kwiecień', 'kwietnia'])) {
            $int = 4;
        } elseif(in_array($str, ['maj', 'maja'])) {
            $int = 5;
        } elseif(in_array($str, ['czerwiec', 'czerwca'])) {
            $int = 6;
        } elseif(in_array($str, ['lipiec', 'lipca'])) {
            $int = 7;
        } elseif(in_array($str, ['sierpień', 'sierpnia'])) {
            $int = 8;
        } elseif(in_array($str, ['wrzesień', 'września'])) {
            $int = 9;
        } elseif(in_array($str, ['październik', 'października', 'pażdziernika', 'pażdziernik'])) {
            $int = 10;
        } elseif(in_array($str, ['listopad', 'listopada'])) {
            $int = 11;
        } elseif(in_array($str, ['grudzień', 'grudnia'])) {
            $int = 12;
        }

        return !isset($int) ? NULL : ($leadingZeros ? (($int < 10) ? '0'.$int : $int) : $int);
    }
}