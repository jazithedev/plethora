<?php

namespace Plethora\Cache\Drivers;

use Plethora\Cache;
use Plethora\Config;
use Plethora\Log;

/**
 * @package        Cache
 * @author         Zalazdi
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class FileCacheDriver extends Cache\CacheDriver
{

    /**
     * @access  private
     * @var     integer
     * @since   1.0.0-alpha
     */
    private $defaultLifeTime;

    /**
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $defaultGroup;

    /**
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $path;

    /**
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private $cache = [];

    /**
     * Constructor
     *
     * @access   public
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __construct()
    {
        $this->defaultLifeTime = Config::get('cache.default_life_time', 3600);
        $this->defaultGroup    = Config::get('cache.default_group', 'unset');
        $this->path            = Config::get('cache.path');

        Log::insert('File cache driver class initialized!');
    }

    /**
     * @access   public
     * @param    mixed   $data
     * @param    string  $id
     * @param    string  $group
     * @param    integer $lifeTime
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function set($data, $id, $group = NULL, $lifeTime = NULL)
    {
        $group    = ($group === NULL) ? $this->defaultGroup : $group;
        $lifeTime = ($lifeTime === NULL) ? $this->defaultLifeTime : $lifeTime;

        $aCache = [
            'data'       => $data,
            'life_time'  => $lifeTime,
            'created_at' => time(),
        ];

        $this->saveCache($aCache, $group, $id);
        $this->cache[$group][$id] = $aCache;

        return TRUE;
    }

    /**
     * @access     public
     * @param    string $id
     * @param    string $group
     * @return    boolean
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function get($id, $group = NULL)
    {
        $group = ($group === NULL) ? $this->defaultGroup : $group;

        if(isset($this->cache[$group][$id])) {
            if(!$this->isExpired($this->cache[$group][$id])) {
                return $this->cache[$group][$id]['data'];
            }

            $this->deleteCache($group, $id);
            unset($this->cache[$group][$id]);

            return FALSE;
        }

        $cacheData = $this->getCache($group, $id);

        if($cacheData) {
            if($this->isExpired($cacheData)) {
                $this->deleteCache($group, $id);

                return FALSE;
            }

            $this->cache[$group][$id] = $cacheData;

            return $this->cache[$group][$id]['data'];
        }

        return FALSE;
    }

    /**
     * Clear cache for particular ID and group.
     *
     * @access   public
     * @param    string $id
     * @param    string $group
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function clearCache($id, $group = NULL)
    {
        $group = ($group === NULL) ? $this->defaultGroup : $group;

        $this->deleteCache($group, $id);

        if(isset($this->cache[$group][$id])) {
            unset($this->cache[$group][$id]);
        }

        return TRUE;
    }

    /**
     * @access     public
     * @param    string $group
     * @return    boolean
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function clearGroupCache($group)
    {
        $this->deleteGroup($group);

        if(isset($this->cache[$group])) {
            unset($this->cache[$group]);
        }

        return TRUE;
    }

    /**
     * @access     public
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function clearAllCache()
    {
        $this->deleteAll();

        foreach(array_keys($this->cache) as $name) {
            unset($this->cache[$name]);
        }
    }

    /**
     * @access     private
     * @param    string $group
     * @param    string $id
     * @return    string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    private function generatePath($group, $id)
    {
        return $this->path.$group.'/'.$id.'.txt';
    }

    /**
     * @access     private
     * @param    array $cache
     * @return    boolean
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    private function isExpired(array $cache)
    {
        return ($cache['life_time'] !== 0 && $cache['life_time'] + $cache['created_at'] < time()) ? TRUE : FALSE;
    }

    /**
     * @access     private
     * @param    mixed  $data
     * @param    string $group
     * @param    string $id
     * @return    boolean
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    private function saveCache($data, $group, $id)
    {
        Log::insert($group.'::'.$id.' cache was saved.');

        if(!is_dir(PATH_CACHE.'/'.$group)) {
            mkdir(PATH_CACHE.'/'.$group);
        }

        file_put_contents($this->generatePath($group, $id), serialize($data));

        return TRUE;
    }

    /**
     * @access   private
     * @param    string $group
     * @param    string $id
     * @return   mixed
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private function getCache($group, $id)
    {
        if($this->issetCache($group, $id)) {
            return unserialize(file_get_contents($this->generatePath($group, $id)));
        }

        return FALSE;
    }

    /**
     * @access     private
     * @param    string $group
     * @param    string $id
     * @return    boolean
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    private function issetCache($group, $id)
    {
        return (file_exists($this->generatePath($group, $id))) ? TRUE : FALSE;
    }

    /**
     * @access   private
     * @param    string $group
     * @return   bool
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private function issetGroup($group)
    {
        return (is_dir($this->path.$group)) ? TRUE : FALSE;
    }

    /**
     * @access   private
     * @param    string $group
     * @param    string $id
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private function deleteCache($group, $id)
    {
        if($this->issetCache($group, $id)) {
            Log::insert($group.'::'.$id.' cache was deleted.');
            unlink($this->generatePath($group, $id));
        }

        return TRUE;
    }

    /**
     * Delete a group of cache.
     *
     * @access   private
     * @param    string $group
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private function deleteGroup($group)
    {
        Log::insert($group.' cache group was deleted.');

        $path = $this->path.$group;

        if(file_exists($path)) {
            foreach(scandir($path) as $file) {
                if($file != "." && $file != "..") {
                    unlink($this->path.$group.DS.$file);
                }
            }

            rmdir($this->path.$group);
        }

        return TRUE;
    }

    /**
     * Remove all cache files.
     *
     * @access   private
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private function deleteAll()
    {
        Log::insert('All cache was deleted');

        foreach(scandir($this->path) as $group) {
            $this->deleteGroup($group);
        }

        return TRUE;
    }

}