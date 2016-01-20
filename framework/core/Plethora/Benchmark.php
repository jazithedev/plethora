<?php

namespace Plethora;

/**
 * Benchmark class
 *
 * @package        Plethora
 * @author         Zalazdi
 * @author         Krzystof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Benchmark
{
    /**
     * List of all markers.
     *
     * @static
     * @access   private
     * @var      array
     * @since    1.0.0-alpha
     */
    private static $aMarkers = [];

    /**
     * Add new marker.
     *
     * @static
     * @access   public
     * @param    string $sName name of the marker
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function mark($sName)
    {
        self::$aMarkers[$sName] = microtime();
    }

    /**
     * Return the time difference between two marked points
     *
     * @static
     * @access   public
     * @param    string $sPoint1 Name first point
     * @param    string $sPoint2 Name second point
     * @return   float   Time difference in miliseconds
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function elapsedTime($sPoint1, $sPoint2 = '')
    {
        if(!isset(self::$aMarkers[$sPoint1])) {
            return '';
        }

        if(!isset(self::$aMarkers[$sPoint2])) {
            self::$aMarkers[$sPoint2] = microtime();
        }

        list($p1m, $p1s) = explode(' ', self::$aMarkers[$sPoint1]);
        list($p2m, $p2s) = explode(' ', self::$aMarkers[$sPoint2]);

        return number_format(($p2m + $p2s) - ($p1m + $p1s), 4) * 100;
    }

    /**
     * Get all markers.
     *
     * @static
     * @access   public
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function getAllMarks()
    {
        return static::$aMarkers;
    }
}
