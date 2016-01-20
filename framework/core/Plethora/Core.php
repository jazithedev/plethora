<?php

namespace Plethora;

/**
 * Main class initializing Plethora Framework.
 *
 * @author           Krzysztof Trzos
 * @copyright    (c) 2016, Krzysztof Trzos
 * @since            1.0.0-alpha
 * @version          1.0.0-alpha
 */
class Core
{

    /**
     * @var    string
     * @since  1.0.0-alpha
     */
    const FW_VERSION = '1.0.0-alpha';

    /**
     * @var    string
     * @since  1.0.0-alpha
     */
    const EXT = '.php';

    /**
     * @var    string
     * @since  1.0.0-alpha
     */
    const MODE_DEVELOPMENT = 'development';

    /**
     * @var    string
     * @since  1.0.0-alpha
     */
    const MODE_PRODUCTION = 'production';

    /**
     * @static
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function getAppMode()
    {
        return Config::get('base.mode');
    }

    /**
     * @static
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function getAppName()
    {
        return Config::get('base.app_name');
    }

    /**
     * @static
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function getVersion()
    {
        return static::FW_VERSION;
    }

    /**
     * Return all languages of particular application.
     *
     * @static
     * @access   public
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function getLanguages()
    {
        return Config::get('base.languages');
    }

    /**
     * Get main language of the actual application.
     *
     * @static
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function getMainLanguage()
    {
        $aLangs = Config::get('base.languages');

        return array_shift($aLangs);
    }

    /**
     * Function which starts web application.
     *
     * @static
     * @access   public
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function startApp()
    {
        if(!file_exists(PATH_APP)) {
            throw new \Exception('Application directory does not exist.');
        }

        // initialize basic functionalities
        Router::loadModulesList();
        DB::create();
        Cache::factory();
        Session::init();
        Router::factory();

        // 2nd mark added to global 'indexView::php' view
        Benchmark::mark('start');

        echo Router::getInstance()->executeAction();
    }

}