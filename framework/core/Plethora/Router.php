<?php

namespace Plethora;

/**
 * Routing main class.
 *
 * @package        Plethora
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Router
{
    /**
     * Router instance created by ::factory() method.
     *
     * @static
     * @access  private
     * @var     Router
     * @since   1.0.0-alpha
     */
    private static $instance = NULL;

    /**
     * All routes list.
     *
     * @static
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private static $routesList;

    /**
     * Controller object.
     *
     * @access  private
     * @var     Controller
     * @since   1.0.0-alpha
     */
    private $controller;

    /**
     * Controller name
     *
     * @static
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private static $controllerName;

    /**
     * Action name
     *
     * @static
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private static $action;

    /**
     * Current URL
     *
     * @static
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private static $currentURL;

    /**
     * Current route
     *
     * @static
     * @access  private
     * @var     Route
     * @since   1.0.0-alpha
     */
    private static $currentRoute;

    /**
     * Current route name.
     *
     * @static
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private static $currentRouteName;

    /**
     * List of current route parameters.
     *
     * @static
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private static $currentRouteParameters = [];

    /**
     * Variable which stores modules list
     *
     * @static
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private static $modules = NULL;

    /**
     * @static
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private static $lang = NULL;

    /**
     * Default language based on base cofig file.
     *
     * @static
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private static $defaultLang = NULL;

    /**
     * This flag is used to lock/unlock the ability to modify routes.
     *
     * @access  private
     * @var     boolean
     * @since   1.0.0-alpha
     */
    private static $flagCanModifyRoutes = FALSE;

    /**
     * Factory method.
     *
     * @static
     * @access   public
     * @return   Router
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory()
    {
        if(static::getInstance() === NULL) {
            static::$instance = new Router();
        }
    }

    /**
     * Get Router class instance.
     *
     * @static
     * @access   public
     * @return   Router
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function &getInstance()
    {
        return static::$instance;
    }

    /**
     * Constructor
     *
     * @access     public
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function __construct()
    {
        static::fetchUrl();
        static::prepareLanguage();
        static::loadRoutes();
        static::loadLocalActions();
        static::identifyCurrentRoute();

        // set $_GET values
        $this->setCurrRouteGETvalues();

        // if route is not identified or valid, return 404 error
        if(is_null(static::$currentRoute) || !static::checkRoute(static::$currentRoute)) {
            static::$currentRoute = Route::factory('err404');
            throw new Exception\Code404;
        }

        // create controller object
        $this->controller = new static::$controllerName;

        Log::insert("Router class initialized! Controller: ".static::$controllerName.", Action: ".static::$action);
    }

    /**
     * Prepare default language and modify current URL.
     *
     * @static
     * @access     private
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    private static function prepareLanguage()
    {
        $aLangs         = static::getLangs();
        $aExploded      = explode('/', trim(static::$currentURL, '/'));
        $sStringToCheck = array_shift($aExploded);
        $aCopiedLangs   = array_slice($aLangs, 0, 1);

        static::$defaultLang = array_shift($aCopiedLangs);

        if(in_array($sStringToCheck, $aLangs)) {
            static::$lang       = $sStringToCheck;
            static::$currentURL = '/'.implode('/', $aExploded);
        } else {
            static::$lang       = static::$defaultLang;
            static::$currentURL = '/'.$sStringToCheck;

            if(!empty($aExploded)) {
                static::$currentURL .= '/'.implode('/', $aExploded);
            }
        }
    }

    /**
     * Get all languages for particular application.
     *
     * @static
     * @access     public
     * @return    array
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function getLangs()
    {
        return Config::get('base.languages');
    }

    /**
     * Get current language.
     *
     * @static
     * @access     public
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function getLang()
    {
        return static::$lang;
    }

    /**
     * Set language for global usage.
     *
     * @static
     * @access     public
     * @param    string $sVal
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function setLang($sVal)
    {
        static::$lang = $sVal;
    }

    /**
     * Get default language for this page.
     *
     * @static
     * @access     public
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function getDefaultLang()
    {
        return static::$defaultLang;
    }

    /**
     * Method to prepare controller name.
     *
     * @static
     * @access   private
     * @param    string $sController
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private static function formatController($sController)
    {
        if(!empty($sController)) {
            $sBuildController = '';

            foreach(explode('_', $sController) as $sPart) {
                $sBuildController .= ucfirst($sPart).'_';
            }

            $sController = str_replace('_', '\\', rtrim($sBuildController, '_'));
        }

        return $sController;
    }

    /**
     * Load all modules list.
     *
     * @static
     * @access  public
     * @return  array
     * @throws  Exception\Router
     * @since   1.0.0-alpha
     * @version 1.0.0-alpha
     */
    public static function loadModulesList()
    {
        if(static::$modules === NULL) {
            static::$modules = [];

            foreach(Config::get('modules') as $sGroupName => $aGroup) {
                foreach($aGroup as $sModuleName => $aModuleData) {
                    if(!isset(static::$modules[$sModuleName])) {
                        $aModuleData = array_merge_recursive($aModuleData, ['path' => $sGroupName.DS.$sModuleName]);

                        static::$modules[$sModuleName] = $aModuleData;
                    } else {
                        throw new Exception\Router('Module "'.$sModuleName.'" has been loaded earlier! You probably load two modules with the same name.');
                    }
                }
            }
        }

        return static::$modules;
    }

    /**
     * Returns modules list.
     *
     * @static
     * @access     public
     * @return    array
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function getModules()
    {
        return static::$modules;
    }

    /**
     * Get module path.
     *
     * @static
     * @access     array
     * @param    string $sModule Name of the module.
     * @return    string  Path to the particular module.
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function getModuleData($sModule)
    {
        return Helper\Arrays::get(static::getModules(), $sModule);
    }

    /**
     * Get module path.
     *
     * @static
     * @access     public
     * @param    string $sModule Name of the module.
     * @return    string  Path to the particular module.
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function getModulePath($sModule)
    {
        $aModule = static::getModuleData($sModule);

        return PATH_MODULES.$aModule['path'];
    }

    /**
     * Returns modules names list.
     *
     * @static
     * @access     public
     * @return    array
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function getModulesNames()
    {
        return array_keys(static::getModules());
    }

    /**
     * @static
     * @access   public
     * @param    string $moduleName
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function hasModule($moduleName)
    {
        return in_array($moduleName, static::getModulesNames());
    }

    /**
     * Return base of the URL with "/" char on the end.
     *
     * @static
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function getBase()
    {
        $https      = filter_input(INPUT_SERVER, 'HTTPS');
        $serverName = filter_input(INPUT_SERVER, 'SERVER_NAME');
        $http       = (!empty($https) && $https != 'off') ? 'https://' : 'http://';

        if(empty($serverName) && isset($_SERVER['SERVER_NAME'])) {
            $serverName = $_SERVER['SERVER_NAME'];
        }

        return $http.$serverName;
    }

    /**
     * Get current route name.
     *
     * @static
     * @acces    public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function getCurrentRouteName()
    {
        return static::$currentRouteName;
    }

    /**
     * Get current URL.
     *
     * @static
     * @access     public
     * @return    string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function getCurrentUrl()
    {
        return filter_input(INPUT_SERVER, 'REQUEST_URI');
    }

    /**
     * Get $_GET values from URL
     *
     * @static
     * @acces      private
     * @return    boolean
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    private static function setCurrRouteGETvalues()
    {
        if(is_null(static::$currentRouteName)) {
            return FALSE;
        }

        $oRoute       = static::$currentRoute;
        $sPatternTmp  = $oRoute->getRawURL();
        $sPatternTmp2 = str_replace('(', '', $sPatternTmp);
        $sPattern     = str_replace(')', '', $sPatternTmp2);

        $aTmp = [];
        preg_match_all('/\{[a-zA-Z0-9_]*\}/', $sPattern, $aTmp);
        $aNames = $aTmp[0];
        unset($aTmp);

        if(count($aNames) > 0) {
            foreach($aNames as $i => $value) {
                $aNames[$i] = str_replace(["{", "}"], "", $value);
            }

            $sUrlToSlice = static::$currentURL;

            foreach(preg_split('/\{[a-zA-Z0-9_]*\}/', $sPattern, -1, PREG_SPLIT_OFFSET_CAPTURE) as $sVal) {
                $sUrlToSlice = str_replace($sVal[0], '|', $sUrlToSlice);
            }

            $sUrlToSlice2   = trim($sUrlToSlice, '|');
            $aQueryValues   = explode('|', $sUrlToSlice2);
            $aRouteDefaults = $oRoute->getDefaults();

            if(isset($aQueryValues[0]) && trim(static::$currentURL, '/') != trim($aQueryValues[0], '/')) {
                foreach($aNames as $i => $sVal) {
                    if(isset($aQueryValues[$i])) {
                        $sQueryValue = $aQueryValues[$i];
                    } elseif(isset($aRouteDefaults[$sVal])) {
                        $sQueryValue = $aRouteDefaults[$sVal];
                    } else {
                        $sQueryValue = NULL;
                    }

                    static::setCurrentRouteParams($sVal, $sQueryValue);

                    if(!is_null($sQueryValue)) {
                        static::$flagCanModifyRoutes = TRUE;

                        switch($sVal) {
                            case 'controller':
                                static::$currentRoute->controller = static::formatController($sQueryValue);
                                break;
                            case 'action':
                                static::$currentRoute->action = ucfirst($sQueryValue);
                                break;
                        }

                        static::$flagCanModifyRoutes = FALSE;
                    }
                }
            }
        }

        return TRUE;
    }

    /**
     * Get all routes from configs files and save it in private var
     *
     * @static
     * @acces      public
     * @param      string $sModule
     * @return     string
     * @throws     Exception\Fatal
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function getModuleGroup($sModule)
    {
        $aModulesFromConfig = Config::get('modules');

        if($aModulesFromConfig !== NULL) {
            foreach($aModulesFromConfig as $sModuleGroupName => $aModules) {
                if(isset($aModules[$sModule])) {
                    return $sModuleGroupName;
                }
            }
        }

        throw new Exception\Fatal('There is no loaded module with name "'.$sModule.'"!');
    }

    /**
     * Load all local actions.
     *
     * @static
     * @acces      private
     * @return    boolean
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    private static function loadLocalActions()
    {
        foreach(static::$modules as $sModuleName => $aModuleData) {
            $sModulePath = $aModuleData['path'];

            if(!in_array($sModulePath, ['.', '..'])) {
                $sDir      = PATH_MODULES.$sModulePath.DS.'config';
                $sFilePath = $sDir.DS.'local_actions.php';

                if(file_exists($sDir) && file_exists($sFilePath)) {
                    Config::get($sModuleName.'.local_actions', NULL);
                }
            }
        }

        return TRUE;
    }

    /**
     * Get all routes from configs files and save it in private var
     *
     * @static
     * @acces    private
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private static function loadRoutes()
    {
        static::$flagCanModifyRoutes = TRUE;

        $queryVariables = NULL;

        # load routes from app
        Config::get('routing.routes', []);

        # load routes from modules
        foreach(static::$modules as $moduleName => $moduleData) {
            $modulePath = $moduleData['path'];

            if(!in_array($modulePath, ['.', '..'])) {
                if(file_exists(PATH_MODULES.$modulePath.DS.'config') && file_exists(PATH_MODULES.$modulePath.DS.'config'.DS.'routing.php')) {
                    Config::get($moduleName.'.routing.routes');
                }
            }
        }

        # get list of all routes
        static::$routesList = Router\Creator::getRoutes();
        Router\Creator::destroy();

        # make some operations on all routes
        foreach(static::$routesList as &$route) {
            /* @var $route Route */
            $rawUrl           = str_replace(['(', ')'], ['((', ')|())'], $route->getRawURL());
            $url              = $rawUrl;
            $routeParamsTypes = $route->getParameterTypes();

            preg_match_all('/\{[a-zA-Z0-9_]*\}/', $rawUrl, $queryVariables);
            $queryVariables = $queryVariables[0];

            if(count($queryVariables) > 0) {
                foreach($queryVariables as $value) {
                    $varName   = str_replace(["{", "}"], "", $value);
                    $replaceTo = "(.+)";

                    # Types of requirements
                    if(isset($routeParamsTypes) && isset($routeParamsTypes[$varName])) {
                        // number
                        if($routeParamsTypes[$varName] == "number") {
                            $replaceTo = "([0-9]+)";
                        } // string
                        elseif($routeParamsTypes[$varName] == "string") {
                            $replaceTo = "(.+)";
                        } // for specific cases, ex: test1|test2
                        elseif(strstr($routeParamsTypes[$varName], "|")) {
                            $replaceTo = "(".$routeParamsTypes[$varName].")";
                        } // other
                        else {
                            $replaceTo = "(".$routeParamsTypes[$varName].")";
                        }
                    }
                    $url = str_replace($value, $replaceTo, $url);
                }
            }

            $route->url = $url;
        }

        static::$flagCanModifyRoutes = FALSE;

        Log::insert('Routes processed.');
    }

    /**
     * Adding new routes for router and returns this particular route object
     *
     * @static
     * @access   public
     * @param    string $sRouteName
     * @param    array  $sRawURL
     * @return   \Plethora\Router\Creator
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function addRoute($sRouteName, $sRawURL)
    {
        return Router\Creator::factory($sRouteName, $sRawURL);
    }

    /**
     * Get single route instance.
     *
     * @static
     * @access      public
     * @param     string $routeName
     * @return     Route
     * @throws   Exception\Router
     * @since       1.0.0-alpha
     * @version     1.0.0-alpha
     */
    public static function getRoute($routeName)
    {
        if(isset(static::$routesList[$routeName])) {
            return static::$routesList[$routeName];
        } else {
            throw new Exception\Router('Route with name "'.$routeName.'" does not exist.');
        }
    }

    /**
     * Get all routes.
     *
     * @static
     * @access   public
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function getRoutes()
    {
        return static::$routesList;
    }

    /**
     * Fetching URL. Get path info and get a route.
     *
     * @static
     * @access     private
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    private static function fetchUrl()
    {
        $sLinkParam = filter_input(INPUT_GET, 'linktoframework');

        if(defined('FORCE_GET_PARAM')) {
            $sLinkParam = trim(${'_GET'}['linktoframework'], '/'); // !!! Only for OTHER custom purposes
        }

        static::$currentURL = '/'.$sLinkParam;

        Log::insert('Current URL ('.static::$currentURL.') fetched.');
    }

    /**
     * Identify and return current route (based on URL from fetchUrl)
     *
     * @static
     * @access     private
     * @return    array
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    private static function identifyCurrentRoute()
    {
        foreach(static::$routesList as $routeName => $route) {
            /* @var $route Route */

            if(preg_match('%^'.$route->getURL().'$%', static::$currentURL)) {
                Log::insert('Route identified: "'.$routeName.'"!');

                static::$currentRoute     = $route;
                static::$currentRouteName = $routeName;

                return static::$currentRoute;
            }
        }

        return FALSE;
    }

    /**
     * Checking if identified route is valid.
     *
     * @static
     * @access   private
     * @param    Route $oRoute
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private static function checkRoute(Route $oRoute)
    {
        $aRouteDefaults = $oRoute->getDefaults();

        // relocation
        if(Helper\Arrays::path($aRouteDefaults, 'relocate', FALSE) !== FALSE) {
            static::relocate($aRouteDefaults['relocate']);
        }

        // check controller
        static::checkControllerExistance($oRoute->getController(), Helper\Arrays::path($aRouteDefaults, 'package', NULL));

        // check action
        static::checkActionExistance($oRoute->getAction());

        return TRUE;
    }

    /**
     * Checks if particular controller exists.
     *
     * @static
     * @param    string $sControllerName
     * @param    string $sPackage
     * @return   boolean
     * @throws   Exception\Router
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private static function checkControllerExistance($sControllerName, $sPackage = NULL)
    {
        $sControllerClassName = '\\Controller\\'.((!is_null($sPackage)) ? $sPackage.'\\' : '').$sControllerName;

        if(class_exists($sControllerClassName)) {
            static::$controllerName = $sControllerClassName;

            return TRUE;
        }

        $sMsg = 'Controller "'.$sControllerClassName.'" do not exist.';
        Log::insert($sMsg, 'ERROR');

        if(Core::getAppMode() == Core::MODE_DEVELOPMENT) {
            throw new Exception\Router($sMsg);
        } else {
            return FALSE;
        }
    }

    /**
     * Checks if particular action for current controller exist.
     *
     * @static
     * @param    string $sActionName
     * @return   boolean
     * @throws   Exception\Router
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private static function checkActionExistance($sActionName)
    {
        $sFullActionName = 'action'.$sActionName;

        if(method_exists(static::$controllerName, $sFullActionName)) {
            static::$action = $sFullActionName;

            return TRUE;
        }

        $sMsg = 'Action "'.$sFullActionName.'" in "'.static::$controllerName.'" controller does not exist.';
        Log::insert($sMsg, 'ERROR');

        if(Config::get('base.mode') == 'development') {
            throw new Exception\Router($sMsg);
        } else {
            return FALSE;
        }
    }

    /**
     * Return current controller name
     *
     * @static
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function getControllerName()
    {
        return static::$currentRoute->getController();
    }

    /**
     * Return current controller object
     *
     * @access   public
     * @return   Controller
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Return current action name.
     *
     * @static
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function getActionName()
    {
        return static::$action;
    }

    /**
     * Get action of particular controller.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function executeAction()
    {
        return $this->getController()->createResponse();
    }

    /**
     * Returns current route URL.
     *
     * @static
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function currentUrl()
    {
        return static::getCurrentRoute()->url(static::getParams());
    }

    /**
     * Returns current route path.
     *
     * @static
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function currentPath()
    {
        return static::getCurrentRoute()->path(static::getParams());
    }

    /**
     * @access   public
     * @param    string $sURL
     * @param    string $sAttrName
     * @param    string $sAttrValue
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function addAttrToURL($sURL, $sAttrName, $sAttrValue)
    {
        return $sURL.((strpos($sURL, '?') === FALSE) ? '?' : '&').$sAttrName.'='.$sAttrValue;
    }

    /**
     * Get URL / path with updated query parameters.
     *
     * @static
     * @access  public
     * @param   string $sURL
     * @param   array  $aParams
     * @param   array  $aIgnoreParams
     * @return  string
     * @since   1.0.0-alpha
     * @version 1.0.0-alpha
     */
    public static function getUrlWithQueryParams($sURL, array $aParams, array $aIgnoreParams = [])
    {
        $aUpdatedParams = array_merge(static::getQueryStringParams(), $aParams);

        // get URL without parameters
        preg_match('/^(.*)[?]+/', $sURL, $aMatches);

        if(count($aMatches) > 0) {
            $sURL = rtrim($aMatches[0], '?');
        }

        // if there are any params to add
        if(count($aUpdatedParams) > 0) {
            $aReturn = [];

            foreach($aUpdatedParams as $sKey => $sValue) {
                if(!in_array($sKey, $aIgnoreParams)) {
                    $aReturn[] = implode('=', [$sKey, $sValue]);
                }
            }

            if(count($aReturn) > 0) {
                $sURL .= '?'.implode('&', $aReturn);
            }
        }

        // return updated URL
        return $sURL;
    }

    /**
     * Get current URL with updated query parameters.
     *
     * @static
     * @access  public
     * @param   array $aParams
     * @param   array $aIgnoreParams
     * @return  string
     * @since   1.0.0-alpha
     * @version 1.0.0-alpha
     */
    public static function currentUrlWithQueryParams(array $aParams, array $aIgnoreParams = [])
    {
        return static::getUrlWithQueryParams(static::currentUrl(), $aParams, $aIgnoreParams);
    }

    /**
     * Get all query string ($_GET) parameters.
     *
     * @static
     * @access  public
     * @return  array
     * @since   1.0.0-alpha
     * @version 1.0.0-alpha
     */
    public static function getQueryStringParams()
    {
        $aParams = filter_input_array(INPUT_GET);

        unset($aParams['linktoframework']);

        return $aParams;
    }

    /**
     * @static
     * @access   public
     * @param    string $sUrl
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function relocate($sUrl)
    {
        header('Location: '.$sUrl);
        die;
    }

    /**
     * Relocate to page of particular route name.
     *
     * @static
     * @access   public
     * @param    string $sRouteName
     * @param    array  $aParams
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function relocateToRoute($sRouteName, array $aParams = [])
    {
        $sURL = Route::factory($sRouteName)->url($aParams);

        static::relocate($sURL);
    }

    /**
     * Set param of current route which is based on URL.
     *
     * @static
     * @access     private
     * @param    string $sKey
     * @param    string $sValue
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    private static function setCurrentRouteParams($sKey, $sValue)
    {
        static::$currentRouteParameters[$sKey] = $sValue;
    }

    /**
     * Get current route parameters.
     *
     * @static
     * @access   private
     * @param    string      $sKey
     * @param    bool|string $sDefaultValue
     * @return   bool|string
     * @throws   Exception\Fatal
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function getParam($sKey, $sDefaultValue = FALSE)
    {
        if(isset(static::$currentRouteParameters[$sKey])) {
            return static::$currentRouteParameters[$sKey];
        } else {
            if(!in_array($sDefaultValue, [NULL, FALSE]) && !is_string($sDefaultValue) && !is_int($sDefaultValue)) {
                throw new Exception\Fatal('Default value of Router param getter must be a string!');
            }

            return $sDefaultValue;
        }
    }

    /**
     * Get all params of current route.
     *
     * @static
     * @access   private
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function getParams()
    {
        return static::$currentRouteParameters;
    }

    /**
     * Check if routes can be modified.
     *
     * @static
     * @access     public
     * @return    boolean
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function isRouteModifyOn()
    {
        return static::$flagCanModifyRoutes;
    }

    /**
     * Check if current route is a route of a front page.
     *
     * @static
     * @access     public
     * @return    boolean
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function isFrontPage()
    {
        $oHomeRoute        = Route::factory('home');
        $sFrontPagePath    = $oHomeRoute->path();
        $sCurrentRoutePath = static::currentPath();

        return $sFrontPagePath === $sCurrentRoutePath;
    }

    /**
     * Get current route.
     *
     * @static
     * @access     public
     * @return    Route
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function getCurrentRoute()
    {
        return static::$currentRoute;
    }

}
