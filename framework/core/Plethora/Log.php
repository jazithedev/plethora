<?php

namespace Plethora;

/**
 * Logger class.
 *
 * @package        Plethora
 * @author         Zalazdi
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Log
{
    const ERROR = 'ERROR';
    const DEBUG = 'DEBUG';
    const INFO  = 'INFO';

    /**
     * File path
     *
     * @static
     * @access   private
     * @var      string
     * @since    1.0.0-alpha
     */
    private static $logPath;

    /**
     * Handler to log file.
     *
     * @static
     * @access  private
     * @var     resource
     * @since   1.0.0-alpha
     */
    private static $handle = NULL;

    /**
     * Constructor
     *
     * Get a file from config folder (year/month/day.log). If not exsits, create it.
     *
     * @static
     * @access   public
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory()
    {
        $year  = date('Y');
        $month = date('n');
        $day   = date('j');

        if(!file_exists(PATH_APP)) {
            return FALSE;
        }

        if(!file_exists(PATH_LOG)) {
            mkdir(PATH_LOG, 0755);
        }

        if(!is_dir(PATH_LOG.$year)) {
            mkdir(PATH_LOG.$year, 0755);
        }

        if(!is_dir(PATH_LOG.$year.'/'.$month)) {
            mkdir(PATH_LOG.$year.'/'.$month, 0755);
        }

        self::$logPath = PATH_LOG.$year.'/'.$month.'/'.$day.'.log';
        self::$handle  = fopen(self::$logPath, 'a+');
        self::insert('Logger class initialized');

        return TRUE;
    }

    /**
     * Destructor. Close a file handler.
     *
     * @static
     * @access   public
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function destruct()
    {
        if(self::$handle !== NULL) {
            fwrite(self::$handle, "\n");
            fclose(self::$handle);
        }
    }

    /**
     * Log message
     *
     * @static
     * @access   public
     * @param    string $sMessage Message
     * @param    string $sLevel   Level (default DEBUG)
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function insert($sMessage, $sLevel = 'DEBUG')
    {
        if(self::$handle === NULL) {
            $bFactoryResult = static::factory();

            if(!$bFactoryResult) {
                return FALSE;
            }
        }

        if(in_array($sLevel, [static::DEBUG, static::ERROR, static::INFO])) {
            $sMessage = $sLevel.' - '.date("H:i:s").' - '.$sMessage."\r\n";

            if(is_resource(self::$handle)) {
                fwrite(self::$handle, $sMessage);
            }
        } else {
            return FALSE;
        }
    }

}
