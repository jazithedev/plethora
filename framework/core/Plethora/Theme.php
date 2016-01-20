<?php

namespace Plethora;

/**
 * Main class used to controll particular theme for webapplication.
 *
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @package        Plethora
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Theme
{
    /**
     * @since    1.0.0-alpha
     */
    const COMMON_NAME = '_common';

    /**
     * Name of current theme.
     *
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private static $theme;

    /**
     * List of all themes available for particular application.
     *
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private static $themesList = [];

    /**
     * Initialize theme.
     *
     * @static
     * @access   public
     * @param    $type
     * @throws   Exception\Fatal
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private static function init($type)
    {
        // set theme
        $theme = Config::get('base.theme.'.$type);

        if(empty($theme)) {
            throw new Exception\Fatal('Frontend or backend theme was not defined in main config file.');
        }

        static::$theme = $theme;

        // get list of themes
        foreach(scandir(PATH_THEMES) as $dir) {
            if(!in_array($dir, ['.', '..'])) {
                static::$themesList[$dir] = 'themes/'.$dir;
            }
        }

        // check if files for the current used theme exists
        if(!isset(static::$themesList[static::$theme])) {
            throw new Exception\Fatal(__('Files for ":themename" theme do not exist.', ['themename' => static::$theme]));
        }
    }

    /**
     * Initialize frontend theme.
     *
     * @static
     * @access   public
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function initFrontend()
    {
        static::init('frontend');
    }

    /**
     * Initialize backend theme.
     *
     * @static
     * @access   public
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function initBackend()
    {
        static::init('backend');
    }


    /**
     * @static
     * @access   public
     * @param    string $sThemeName
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function setTheme($sThemeName)
    {
        static::$theme = $sThemeName;
    }

    /**
     * Get current theme name.
     *
     * @static
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function getTheme()
    {
        return static::$theme;
    }

    /**
     * Get current theme path name.
     *
     * @static
     * @param    string $sThemeName
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function getThemePath($sThemeName = NULL)
    {
        if($sThemeName === NULL) {
            $sThemeName = static::$theme;
        }

        return Helper\Arrays::get(static::$themesList, $sThemeName);
    }

    /**
     * Get path to the common directory.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function getPathToCommon()
    {
        return '/themes/'.static::COMMON_NAME.'/';
    }
}