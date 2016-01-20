<?php

namespace Plethora;

/**
 * Session class.
 *
 * @package        Plethora
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Session
{

    /**
     * Session variables
     *
     * @static
     * @access  protected
     * @var     array
     * @since   1.0.0-alpha
     */
    protected static $vars = [];

    /**
     * Session id (sha1 code).
     *
     * @static
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected static $id;

    /**
     * User IP
     *
     * @static
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected static $ip;

    /**
     * User browser
     *
     * @static
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected static $userAgent;

    /**
     * Array with session settings from config file
     *
     * @static
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private static $sessionSettings;

    /**
     * Tels destructor to delete (or not) flash message
     *
     * @static
     * @access  private
     * @var     boolean
     * @since   1.0.0-alpha
     */
    private static $blockFlashRemoval = TRUE;

    /**
     * Session initializing static method.
     *
     * @static
     * @access   public
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function init()
    {
        static::$ip              = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
        static::$userAgent       = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT');
        static::$sessionSettings = Config::get('session');
        static::checkCookie();
    }

    /**
     * Clears all temporary data stored in the session files (for example, flash messages).
     *
     * @static
     * @access   public
     * @return   int|boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function clearTempData()
    {
        // delete flash message
        if(self::$blockFlashRemoval == TRUE && isset(static::$vars['flash'])) {
            unset(static::$vars['flash']);
        }

        // update session
        return static::update();
    }

    /**
     * Remove cookie, file and create new session
     *
     * @static
     * @access   public
     * @param    string $sKey
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function destroy($sKey = NULL)
    {
        // destroy entire cookie
        if(empty($sKey)) {
            setcookie(self::$sessionSettings['cookie_name'], self::$id, time() + self::$sessionSettings['expire'], "/", NULL, NULL, TRUE);

            static::$vars = [
                'ip'         => self::$ip,
                'user_agent' => self::$userAgent,
            ];
        } // destroy one key from cookie
        elseif(!is_null(self::get($sKey))) {
            unset(static::$vars[$sKey]);
        }

        static::update();
    }

    /**
     * Updates session.
     *
     * @static
     * @access   public
     * @return   int|boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    protected static function update()
    {
        setcookie(self::$sessionSettings['cookie_name'], self::$id, time() + self::$sessionSettings['expire'], "/", NULL, NULL, TRUE);

        return file_put_contents(self::$sessionSettings['path'].sha1(self::$id), serialize(static::$vars));
    }

    /**
     * Set value to session.
     *
     * @access   public
     * @param    string $name
     * @param    mixed  $value
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __set($name, $value)
    {
        self::set($name, $value);
    }

    /**
     * Get value from session.
     *
     * @access   public
     * @param    string $name
     * @return   mixed
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __get($name)
    {
        return self::get($name);
    }

    /**
     * Checking cookie
     *
     * @static
     * @access   protected
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    protected static function checkCookie()
    {
        if(filter_input(INPUT_COOKIE, self::$sessionSettings['cookie_name']) !== NULL) {
            self::$id = filter_input(INPUT_COOKIE, self::$sessionSettings['cookie_name']);

            if(file_exists(self::$sessionSettings['path'].sha1(self::$id))) {
                $aData = unserialize(file_get_contents(self::$sessionSettings['path'].sha1(self::$id)));

                // Check a ip and useragent
                if($aData['ip'] == self::$ip && $aData['user_agent'] == self::$userAgent) {
                    static::$vars = $aData;

                    // Set new cookie
                    setcookie(self::$sessionSettings['cookie_name'], self::$id, time() + self::$sessionSettings['expire'], "/", NULL, NULL, TRUE);

                    // Refresh session file last edit date
                    touch(self::$sessionSettings['path'].sha1(self::$id));

                    return TRUE;
                }
            }
        }

        static::createSession();

        return FALSE;
    }

    /**
     * Create session
     *
     * Send cookie to user and create file
     *
     * @static
     * @access     protected
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    protected static function createSession()
    {
        self::$id     = static::generateHash();
        static::$vars = [
            'ip'         => self::$ip,
            'user_agent' => self::$userAgent,
        ];

//		setcookie(self::$aSessionSettings['cookie_name'], self::$sId, time() + self::$aSessionSettings['expire'], "/", NULL, NULL, TRUE);

        static::update();
    }

    /**
     * Generate hash (id).
     *
     * @static
     * @access   protected
     * @return   string    SHA-1 hash
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    protected static function generateHash()
    {
        return sha1(time().self::$ip.rand(0, 10));
    }

    /**
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function getHash()
    {
        return static::$id;
    }


    /**
     * Garbage collector
     *
     * Delete all files if expired
     *
     * @static
     * @access   public
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function sessionCleaner()
    {
        $dir = dir(self::$sessionSettings['path']);

        while($file = $dir->read()) {
            if(!in_array($file, [".", "..", ".svn"])) {
                $filename = self::$sessionSettings['path'].$file;

                if(time() > (filemtime($filename) + self::$sessionSettings['expire'])) {
                    unlink($filename);
                }
            }
        }

        $dir->close();
    }

    /**
     * Get a value from the Session container object.
     *
     * @static
     * @access     public
     * @param    string $name
     * @return    mixed
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function get($name = NULL)
    {
        if($name !== NULL) {
            return isset(static::$vars[$name]) ? static::$vars[$name] : NULL;
        } else {
            return static::$vars;
        }
    }

    /**
     * Set a value in current user session
     *
     * @static
     * @access   public
     * @param    string $name  new session variable name
     * @param    mixed  $value new session variable value
     * @throws   Exception
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function set($name, $value)
    {
        if($name == 'perm') {
            $msg = __('Name "perm" is reserved and cannot be used!');

            Log::insert($msg, 'ERROR');
            throw new Exception($msg);
        }

        static::$vars[$name] = $value;

        static::update();
    }

    /**
     * Relocate to another page and show alert message.
     *
     * Type of messages:
     * - success
     * - info
     * - warning
     * - danger
     *
     * @static
     * @access   public
     * @param    string $location URL
     * @param    string $content  message content
     * @param    string $type     message type
     * @throws   Exception
     * @throws   Exception\Fatal
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function flash($location, $content, $type = 'success')
    {
        if(!in_array($type, ['success', 'info', 'warning', 'danger'])) {
            throw new Exception\Fatal('Unknown type of flash message ("'.$type.'").');
        }

        self::set("flash", serialize(["content" => $content, "type" => $type]));
        self::$blockFlashRemoval = FALSE;

        if(strpos($location, 'http://') === FALSE) {
            $base = '';
        } else {
            $base = Config::get('routing.base_url');
        }

        Router::relocate($base.$location);
    }

}