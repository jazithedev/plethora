<?php

namespace User;

use Plethora\Cache;
use Plethora\Session;

/**
 * Class LoginFail
 *
 * @author  Krzysztof Trzos <k.trzos@jazi.pl>
 * @package User
 * @since   2.1.2-dev
 * @version 2.1.2-dev
 */
class LoginFail
{
    /**
     * Name of the cache group for login fail.
     *
     * @access private
     * @var    string
     * @since  2.1.2-dev
     */
    private static $cacheName = 'login_fail';

    /**
     * @static
     * @access  public
     * @since   2.1.2-dev
     * @version 2.1.2-dev
     */
    public static function addLoginFail()
    {
        $cachedData = self::getCachedData();

        if($cachedData === FALSE) {
            $cachedData = 1;
        } else {
            $cachedData++;
        }

        self::setCachedData($cachedData, Session::get('ip'));
    }

    /**
     * Get data about failed login operations from cache.
     *
     * @static
     * @access  private
     * @return  integer
     * @since   2.1.2-dev
     * @version 2.1.2-dev
     */
    public static function getCachedData()
    {
        $ip        = Session::get('ip');
        $cacheData = Cache::get($ip, static::$cacheName);

        return $cacheData;
    }

    /**
     * Set new data about failed login for particular IP address.
     *
     * @access  private
     * @param   integer $data
     * @param   string  $ip
     * @return  boolean
     * @since   2.1.2-dev
     * @version 2.1.2-dev
     */
    private static function setCachedData($data, $ip)
    {
        return Cache::set($data, $ip, static::$cacheName, 60*15);
    }
}