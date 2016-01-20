<?php

namespace Plethora;

/**
 * Single route class.
 *
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @package        Plethora
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Route
{

    /**
     * Raw URL for particular route.
     *
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $rawURL = '';

    /**
     * Processed URL (by Router) for particular route.
     *
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $url = '';

    /**
     * Route controller class.
     *
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $controller = '';

    /**
     * Route action name.
     *
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $action = '';

    /**
     * Route name.
     *
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $name = '';

    /**
     * All route parameters types.
     *
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private $parametersTypes = [];

    /**
     * Route default values for all parameters.
     *
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private $defaults = [];

    /**
     * Route access permissions.
     *
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private $permissions = [];

    /**
     * Array of access functions. It stores all function which checks if logged
     * user has access to particular route.
     *
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private $accessFunctions = [];

    /**
     * Variable which stores information about whether currently logged user has
     * access to this part of page (route).
     *
     * @access  private
     * @var     boolean
     * @since   1.0.0-alpha
     */
    private $hasAccess = NULL;

    /**
     * Factory method.
     *
     * @static
     * @access   public
     * @param    string $sRouteName
     * @return   Route
     * @throws   Exception\Router
     * @sicne    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory($sRouteName)
    {
        if(empty($sRouteName)) {
            throw new Exception\Router(__('Name of the route cannot be empty!'));
        }

        return Router::getRoute($sRouteName);
    }

    /**
     * Route constructor.
     *
     * @access   public
     * @param    string $sRouteName
     * @sicne    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __construct($sRouteName)
    {
        $this->name = $sRouteName;
    }

    /**
     * Setting values
     *
     * @access   public
     * @param    string $name
     * @param    mixed  $value
     * @throws   Exception\Fatal
     * @sicne    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __set($name, $value)
    {
        if(Router::isRouteModifyOn() === FALSE) {
            throw new Exception\Fatal('Modifying route in wrong place. Routes can be edited only in routing.php config files.');
        }

        if(property_exists($this, $name)) {
            switch($name) {
                case 'action':
                    if(!is_string($value)) {
                        throw new Exception\Fatal('Wrong value for route action in "'.$this->name.'" route.');
                    }

                    break;
                case 'controller':
                    if(!is_string($value)) {
                        throw new Exception\Fatal('Wrong value for route controller in "'.$this->name.'" route.');
                    }

                    break;
                case 'defaults':
                    if(!is_array($value)) {
                        throw new Exception\Fatal('Wrong value for route default values in "'.$this->name.'" route.');
                    }

                    break;
                case 'permissions':
                    if(!is_array($value)) {
                        throw new Exception\Fatal('Wrong value for route permissions in "'.$this->name.'" route.');
                    }

                    break;
                case 'parametersTypes':
                    if(!is_array($value)) {
                        throw new Exception\Fatal('Wrong value for route parameters types in "'.$this->name.'" route.');
                    }

                    break;
                case 'url':
                case 'rawURL':
                    if(!is_string($value)) {
                        throw new Exception\Fatal('Wrong value for route URL (or raw URL) in "'.$this->name.'" route.');
                    }

                    break;
            }

            $this->$name = $value;
        }
    }

    /**
     * Return URL of current route
     *
     * @access   public
     * @param    array $args
     * @return   string
     * @sicne    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function url(array $args = [])
    {
        $aTemp   = [];
        $sRawURL = $this->rawURL;

        preg_match_all('/\{[a-zA-Z0-9_]*\}/', $sRawURL, $aTemp);
        $GET_variables = $aTemp[0];

        unset($aTemp);

        if(count($GET_variables) > 0) {
            foreach($GET_variables as $mValue) {
                $sVaName = str_replace(["{", "}"], "", $mValue);

                if(isset($args[$sVaName])) {
                    $sReplaceTo = $args[$sVaName];
                    unset($args[$sVaName]);
                } else {
                    $sReplaceTo = NULL;
                }

                $sRawURL = str_replace($mValue, $sReplaceTo, $sRawURL);
            }
        }

        $sReplacedURL = str_replace(['(', ')'], ['', ''], $sRawURL);
        $sURL         = rtrim($sReplacedURL, '/');

        if(!empty($args)) {
            foreach($args as $sKey => $sValue) {
                if(!empty($sValue)) {
                    Router::addAttrToURL($sURL, $sKey, $sValue);
                }
            }
        }

        $sDefaultLang = Router::getLang();
        $sLang        = Helper\Arrays::get($args, 'lang', $sDefaultLang);
        $aLangs       = Config::get('base.languages');
        $sFirstLang   = array_shift($aLangs);

        if($sLang == $sFirstLang) {
            return Router::getBase().$sURL;
        } else {
            return Router::getBase().'/'.$sLang.$sURL;
        }
    }

    /**
     * Get path for particular route and parameters.
     *
     * @access   public
     * @param    array $aArgs
     * @return   string
     * @sicne    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function path(array $aArgs = [])
    {
        $sURL  = $this->url($aArgs);
        $sPath = str_replace(Router::getBase(), '', $sURL);

        if(substr($sPath, 0, 1) !== '/') {
            $sPath = '/'.$sPath;
        }

        return $sPath;
    }

    /**
     * Method used to redirect client to page with particular arguments.
     *
     * @access   public
     * @param    array $aArgs
     * @sicne    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function redirectTo(array $aArgs = [])
    {
        Router::relocate($this->url($aArgs));
    }

    /**
     * Generate backend URL.
     *
     * @static
     * @access   public
     * @param    string $controller
     * @param    string $action
     * @param    string $id
     * @param    string $extra
     * @return   string
     * @sicne    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function backendUrl($controller = NULL, $action = NULL, $id = NULL, $extra = NULL)
    {
        $params = [];

        if(!is_null($controller)) {
            $params['controller'] = str_replace('\\', '_', strtolower($controller));
        }

        if(!is_null($action)) {
            $params['action'] = $action;
        }

        if(!is_null($id)) {
            $params['id'] = $id;
        }

        if(!is_null($extra)) {
            $params['extra'] = $extra;
        }

        return static::factory('backend')->url($params);
    }

    /**
     * Get all parameter types.
     *
     * @access   public
     * @return   array
     * @sicne    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getParameterTypes()
    {
        return $this->parametersTypes;
    }

    /**
     * Get all route default values for parameters.
     *
     * @access   public
     * @return   array
     * @sicne    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * Get all route access permissions.
     *
     * @access   public
     * @return   array
     * @sicne    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Get all route access permissions.
     *
     * @access   public
     * @param    array $permissions
     * @return   $this
     * @sicne    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setPermissions(array $permissions)
    {
        $this->permissions = $permissions;

        return $this;
    }

    /**
     * Get route action.
     *
     * @access   public
     * @return   string
     * @sicne    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Get route controller.
     *
     * @access   public
     * @return   string
     * @sicne    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Get roue raw URL.
     *
     * @access   public
     * @return   string
     * @sicne    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getRawURL()
    {
        return $this->rawURL;
    }

    /**
     * Get route URL.
     *
     * @access   public
     * @return   string
     * @sicne    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getURL()
    {
        return $this->url;
    }

    /**
     * Get all access functions.
     *
     * @access   public
     * @return   array
     * @sicne    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getAccessFunctions()
    {
        return $this->accessFunctions;
    }

    /**
     * Check if currently logged user has access to this route.
     *
     * @access   public
     * @param    array $aParams
     * @return   bool
     * @sicne    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function hasAccess(array $aParams = [])
    {
        // check if access was verified previously
        if($this->hasAccess !== NULL) {
            return $this->hasAccess;
        }

        // firstly, check required permissions
        foreach($this->getPermissions() as $sPermission) {
            if(\UserPermissions::hasPerm($sPermission) === FALSE) {
                return $this->hasAccess = FALSE;
            }
        }

        // secondly, check access functions
        foreach($this->getAccessFunctions() as $oFunction) {
            /* @var $oFunction \Closure */
            if($oFunction($this, $aParams) === FALSE) {
                return $this->hasAccess = FALSE;
            }
        }

        // return TRUE = has access
        return $this->hasAccess = TRUE;
    }

}
