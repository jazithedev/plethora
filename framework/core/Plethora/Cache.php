<?php

namespace Plethora;

/**
 * Parent Cache class.
 *
 * @package        Plethora
 * @subpackage     Cache
 * @author         Zalazdi
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Cache
{
    const INFINITY = 0;

    /**
     * @static
     * @access  private
     * @var     integer
     * @since   1.0.0-alpha
     */
    private static $defaultLifeTime;

    /**
     * @static
     * @access  private
     * @var     Cache\CacheDriver
     * @since   1.0.0-alpha
     */
    private static $driver = NULL;

    /**
     * Factory method.
     *
     * @static
     * @access   public
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory()
    {
        self::$defaultLifeTime = Config::get('cache.lifetime', 3600);

        $driverName = ucfirst(Config::get('cache.driver'));
        $driver     = '\\Plethora\\Cache\\Drivers\\'.$driverName.'CacheDriver';

        static::$driver = new $driver;

        Log::insert('Cache type "'.Config::get('cache.driver').'" initialized!');
    }

    /**
     * @static
     * @access   public
     * @param    mixed   $data
     * @param    string  $id
     * @param    string  $group
     * @param    integer $lifeTime
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function set($data, $id, $group = NULL, $lifeTime = NULL)
    {
        return static::$driver->set($data, $id, $group, $lifeTime);
    }

    /**
     * @static
     * @access   public
     * @param    string $id
     * @param    string $group
     * @return   mixed
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function get($id, $group = NULL)
    {
        return static::$driver->get($id, $group);
    }


    /**
     * @static
     * @access   public
     * @param    string $id
     * @param    string $group
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function clearCache($id, $group = NULL)
    {
        return static::$driver->clearCache($id, $group);
    }


    /**
     * @static
     * @access   public
     * @param    string $group
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function clearGroupCache($group)
    {
        return static::$driver->clearGroupCache($group);
    }

    /**
     * @static
     * @access   public
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function clearAllCache()
    {
        return static::$driver->clearAllCache();
    }

}