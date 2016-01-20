<?php

namespace Plethora\Router;

use Plethora\Exception;
use Plethora\Route;

/**
 * Routes creator.
 *
 * @package        Plethora
 * @subpackage     Router
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Creator
{

    /**
     * Creator instance.
     *
     * @static
     * @access  private
     * @var     Creator
     * @since   1.0.0-alpha
     */
    private static $instance;

    /**
     * List of all routes created by this class.
     *
     * @static
     * @access    private
     * @var        array
     * @since     1.0.0-alpha
     */
    private static $routes = NULL;

    /**
     * Last created route name.
     *
     * @static
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private static $lastRoute = NULL;

    /**
     * Factory routes Creator class.
     *
     * @static
     * @access   public
     * @param    string $routeName
     * @param    string $url
     * @return   Creator
     * @throws   Exception\Router
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory($routeName, $url)
    {
        if(isset(static::$routes[$routeName])) {
            throw new Exception\Router(
                __('Route with name ":routename" was added to the routing table twice!', [
                    'routename' => $routeName
                ])
            );
        }

        if(static::$instance === NULL) {
            static::$instance = new Creator();
        }

        $route             = new Route($routeName);
        $route->rawURL     = $url;
        $route->controller = 'Frontend';
        $route->action     = 'Default';

        static::$routes[$routeName] = $route;
        static::$lastRoute          = $routeName;

        return static::$instance;
    }

    /**
     * Destroy all data related with this class. This is used for memory release
     * purposes.
     *
     * @static
     * @access     public
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function destroy()
    {
        static::$instance = NULL;

        foreach(array_keys(static::$routes) as $i) {
            unset(static::$routes[$i]);
        }
    }

    /**
     * Get list of all created routes.
     *
     * @static
     * @access     public
     * @return    array
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function getRoutes()
    {
        return static::$routes;
    }

    /**
     * Set action for last added route.
     *
     * @access     public
     * @param    string $sAction
     * @return    Creator
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setAction($sAction)
    {
        $route = static::$routes[static::$lastRoute];
        /* @var $route Route */

        $route->action = $sAction;

        return $this;
    }

    /**
     * Set controller for last added route.
     *
     * @access     public
     * @param    string $sController
     * @return    Creator
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setController($sController)
    {
        $route = static::$routes[static::$lastRoute];
        /* @var $route Route */

        $route->controller = $sController;

        return $this;
    }

    /**
     * Set all parameters types for last added route.
     *
     * @access     public
     * @param    array $aTypes
     * @return    Creator
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setParametersTypes(array $aTypes)
    {
        $route = static::$routes[static::$lastRoute];
        /* @var $route Route */

        $route->parametersTypes = $aTypes;

        return $this;
    }

    /**
     * Set single parameter type for last added route.
     *
     * @access     public
     * @param    string $paramName
     * @param    string $paramType
     * @return    Creator
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function addParameterType($paramName, $paramType)
    {
        $route = static::$routes[static::$lastRoute];
        /* @var $route Route */

        $aParameterTypes             = $route->getParameterTypes();
        $aParameterTypes[$paramName] = $paramType;

        $route->parametersTypes = $aParameterTypes;

        return $this;
    }

    /**
     * Set all default values of URL parameters for last added route.
     *
     * @access     public
     * @param    array $defaults
     * @return    Creator
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setDefaults(array $defaults)
    {
        $route = static::$routes[static::$lastRoute];
        /* @var $route Route */

        $route->defaults = $defaults;

        return $this;
    }

    /**
     * Add default value of single URL parameter for last added route.
     *
     * @access   public
     * @param    string $paramName
     * @param    string $value
     * @return   Creator
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function addDefault($paramName, $value)
    {
        $route = static::$routes[static::$lastRoute];
        /* @var $route Route */

        $aDefaults             = $route->getDefaults();
        $aDefaults[$paramName] = $value;

        $route->defaults = $aDefaults;

        return $this;
    }

    /**
     * Set access permissions for last added route.
     *
     * @access     public
     * @param    array $permissions
     * @return    Creator
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setPermissions(array $permissions)
    {
        $route = static::$routes[static::$lastRoute];
        /* @var $route Route */

        $route->permissions = $permissions;

        return $this;
    }

    /**
     * Set single access permission for last added route.
     *
     * @access     public
     * @param    string $sParamName
     * @param    string $sPermission
     * @return    Creator
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function addPermission($sParamName, $sPermission)
    {
        $route = static::$routes[static::$lastRoute];
        /* @var $route Route */

        $aPermissions              = $route->getPermissions();
        $aPermissions[$sParamName] = $sPermission;

        $route->permissions = $aPermissions;

        return $this;
    }

    /**
     * Add new access function.
     *
     * @access     public
     * @param    \Closure $oLambda
     * @return    Creator
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function addAccessFunction(\Closure $oLambda)
    {
        $route = static::$routes[static::$lastRoute];
        /* @var $route Route */

        $aFunctions   = $route->getAccessFunctions();
        $aFunctions[] = $oLambda;

        $route->accessFunctions = $aFunctions;

        return $this;
    }

}
