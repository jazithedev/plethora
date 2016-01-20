<?php

namespace Plethora\Cache;

/**
 * Parent of cache drivers
 *
 * @author         Zalazdi
 * @copyright  (c) 2016, Krzysztof Trzos
 * @package        Plethora
 * @subpackage     Cache
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
abstract class CacheDriver {
    abstract function set($data, $id, $group = NULL, $lifeTime = NULL);

    abstract function get($id, $group = NULL);

    abstract function clearCache($id, $group = NULL);

    abstract function clearGroupCache($group);

    abstract function clearAllCache();
}