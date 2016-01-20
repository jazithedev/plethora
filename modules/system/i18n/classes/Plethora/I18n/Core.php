<?php

namespace Plethora\I18n;

use Model;
use Plethora\Cache;
use Plethora\Config;
use Plethora\Helper;
use Plethora\Router;
use Plethora\Exception;

/**
 * @package          I18n
 * @author           Krzysztof Trzos
 * @copyright    (c) 2016, Krzysztof Trzos
 * @since            1.0.0-dev
 * @version          1.0.5-dev
 */
class Core
{

    /**
     * @access  private
     * @var     array
     * @since   1.0.0-dev
     */
    private static $cachedData = NULL;

    /**
     * Get translation for particular string.
     *
     * @static
     * @access   public
     * @param    string $string
     * @param    array  $options
     * @return   string
     * @since    1.0.0-dev
     * @version  1.2.0-dev
     */
    public static function get($string, $options = [])
    {
        $output     = $string;
        $cachedData = static::getCachedData();
        $context    = Helper\Arrays::get($options, 'context', 'no_context');

        if(isset($cachedData[$string]) && isset($cachedData[$string][$context])) {
            $output = $cachedData[$string][$context];
        }

        return $output;
    }

    /**
     * Get whole cached translations data.
     *
     * @access   public
     * @return   array
     * @since    1.0.0-dev
     * @version  1.2.0-dev
     */
    public static function getCachedData()
    {
        if(static::$cachedData === NULL) {
            $cachedData = Cache::get(Router::getLang(), 'i18n');

            if($cachedData === FALSE) {
                $cachedData = [];
            }

            static::$cachedData = $cachedData;
        }

        return static::$cachedData;
    }

    /**
     * Create new translation cache.
     *
     * @static
     * @access   private
     * @since    1.0.0-dev
     * @version  1.2.0-dev
     */
    private static function cacheTranslations()
    {
        $importSystem = Import::factory();
        $translations = $importSystem->importFiles();
        $divided      = static::dividePerLang($translations);

        foreach($divided as $lang => $translationsPerLang) {
            Cache::set($translationsPerLang, $lang, 'i18n', 0);
        }

        self::cacheInfo($divided, $importSystem);
    }

    /**
     * This method creates INFO file with basic information about
     * cached translations.
     *
     * @static
     * @param    string $translations
     * @param    Import $importSystem
     * @since    1.2.0-dev
     * @version  1.2.0-dev
     */
    private static function cacheInfo($translations, Import $importSystem)
    {
        $date  = new \DateTime;
        $langs = array_keys($translations);

        $info                    = [];
        $info['date']            = $date->format('Y-m-d H:i:s');
        $info['langs']           = $langs;
        $info['amount']          = [];
        $info['amount_per_part'] = $importSystem->getCountedValues();

        foreach($langs as $lang) {
            $info['amount'][$lang] = count($translations[$lang]);
        }

        Cache::set($info, 'info', 'i18n', 0);
    }

    /**
     * @static
     * @access   private
     * @param    array $data
     * @return   array
     * @since    1.2.0-dev
     * @version  1.2.0-dev
     */
    private static function dividePerLang(array $data)
    {
        $output = [];

        foreach($data as $context => $translatons) {
            foreach($translatons as $string => $translatedLangs) {
                foreach($translatedLangs as $lang => $translated) {
                    Helper\Arrays::createMultiKeys($output, $lang.':;:'.$string.':;:'.$context, $translated, ':;:');
                }
            }
        }

        return $output;
    }

    /**
     * Clear all I18n module cache.
     *
     * @static
     * @access   public
     * @return   boolean  Returns information whether the language cache has been cleared.
     * @since    1.0.5-dev
     * @version  1.2.0-dev
     */
    public static function clearCache()
    {
        static::$cachedData = NULL;

        $output = Cache::clearGroupCache('i18n');

        return $output;
    }

    /**
     * Reload cache with interface translations.
     *
     * @static
     * @access   public
     * @since    1.2.0-dev
     * @version  1.2.0-dev
     */
    public static function reloadCache()
    {
        static::clearCache();
        static::cacheTranslations();
    }
}
