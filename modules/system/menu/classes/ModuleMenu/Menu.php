<?php

namespace ModuleMenu;

use Plethora\Helper;
use Plethora\Route;
use Plethora\Router;
use Plethora\View;
use Plethora\Exception;

/**
 * Class which is used to generate menu HTML code.
 *
 * @author         Krzysztof Trzos
 * @package        menu
 * @subpackage     classes
 * @since          1.0.0-dev
 * @version        1.3.0-dev
 */
class Menu
{
    /**
     * Menu routes.
     *
     * @access  private
     * @var     array
     * @since   1.3.0-dev
     */
    private $routes = [];

    /**
     * Class indicating active trail in menu.
     *
     * @access  private
     * @var     string
     * @since   1.3.0-dev
     */
    private $activeTrailClass = 'active_trail';

    /**
     * Class indicating active trail in menu.
     *
     * @access  private
     * @var     string
     * @since   1.3.0-dev
     */
    private $submenuClasses = 'submenu';

    /**
     * Menu attributes.
     *
     * @access  private
     * @var     Helper\Attributes
     * @since   1.3.0-dev
     */
    private $attributes = NULL;

    /**
     * Factory method.
     *
     * @access   public
     * @param    array $routes
     * @return   Menu
     * @since    1.3.0-dev
     * @version  1.3.0-dev
     */
    public static function factory(array $routes)
    {
        return new Menu($routes);
    }

    /**
     * Constructor
     *
     * @param    array $routes
     * @since    1.3.0-dev
     * @version  1.3.0-dev
     */
    public function __construct(array $routes)
    {
        $this->routes     = $routes;
        $this->attributes = new Helper\Attributes();
    }


    /**
     * Generate menu as a View object.
     *
     * @static
     * @access   public
     * @param    string $menuMachineName
     * @param    string $menuHeader
     * @return   View
     * @since    1.0.0-dev
     * @version  1.0.0-dev
     */
    public function generate($menuMachineName, $menuHeader = NULL)
    {
        $this->findActiveRoute($this->routes);
        $oMenuView = $this->createNextLevel($this->routes, 0);

        return View::factory('menu/menu_container')
            ->bind('sMenuHeader', $menuHeader)
            ->bind('sMenuMachineName', $menuMachineName)
            ->bind('oContent', $oMenuView);
    }

    /**
     * Create next level of menu.
     *
     * @access   private
     * @param    array   $routes
     * @param    integer $level
     * @return   View
     * @throws   Exception\Router
     * @since    1.0.0-dev
     * @version  1.3.0-dev
     */
    private function createNextLevel(array $routes, $level)
    {
        $entries = [];
        $level++;

        foreach($routes as $route) {
            /* @var $route array */
            /* @var $routeTitle string */
            /* @var $routeName string */
            /* @var $routeParams array */
            /* @var $children array */

            $routeTitle  = $route['title'];
            $routeName   = Helper\Arrays::get($route, 'route_name', NULL);
            $routeParams = Helper\Arrays::get($route, 'route_parameters', []);
            $url         = Helper\Arrays::get($route, 'url', NULL);
            $children    = Helper\Arrays::get($route, 'children', []);

            if($routeName !== NULL) {
                $path = Route::factory($routeName)->path($routeParams);
            } else {
                $path = $url;
            }

            $singleLevel = View::factory('menu/single_level');
            $singleLevel->set('route', $route);
            $singleLevel->set('routeTitle', $routeTitle);
            $singleLevel->set('routeName', $routeName);
            $singleLevel->set('path', $path);
            $singleLevel->set('routeParams', $routeParams);
            $singleLevel->set('classes', $route['classes']);

            if($children !== []) {
                $children = $this->createNextLevel($children, $level);
                /* @var $children View */
            }

            $singleLevel->set('children', $children);

            $entries[] = $singleLevel;
        }

        if($level > 1) {
            return View::factory('menu/submenu')
                ->set('submenuClasses', $this->submenuClasses)
                ->set('level', $level)
                ->set('entries', $entries);
        } else {
            $this->attributes->addToAttribute('class', 'menu_level_1');

            return View::factory('menu/menu')
                ->set('attributes', $this->attributes)
                ->set('entries', $entries);
        }
    }

    /**
     * Find active route in particular menu.
     *
     * @access   private
     * @param    array   $routes
     * @param    integer $parentKey
     * @param    array   $parent
     * @since    1.0.0-dev
     * @version  1.3.0-dev
     */
    private function findActiveRoute(array &$routes, $parentKey = NULL, array $parent = [])
    {
        foreach($routes as $i => &$route) {
            /* @var $route array */
            /* @var $routeName string */
            /* @var $routeParams array */
            /* @var $activeRoutes array */

            if(!isset($route['classes'])) {
                $route['classes'] = [];
            }

            $routeName    = Helper\Arrays::get($route, 'route_name', NULL);
            $url          = Helper\Arrays::get($route, 'url', NULL);
            $routeParams  = Helper\Arrays::get($route, 'route_parameters', []);
            $activeRoutes = Helper\Arrays::get($route, 'active_routes', []);

//            $path        = $url === NULL ? Route::factory($routeName)->path($routeParams) : $url;

            if($routeName !== NULL) {
                $path = Route::factory($routeName)->path($routeParams);
            } else {
                $path = $url;
            }

            $path        = str_replace(Router::getBase(), '', $path);
            $currentPath = Router::getCurrentUrl();

            if(in_array(Router::getCurrentRouteName(), $activeRoutes)) {
                $route['classes'][] = ['current '.$this->activeTrailClass];
            }

            if($parentKey !== NULL) {
                $route['parent_key'] = $parentKey;
                $route['parent']     = $parent;
            }

            if($path === $currentPath) {
                $this->goBackAndSetActive($route);
            }

            if(isset($route['children']) && !empty($route['children'])) {
                $this->findActiveRoute($route['children'], $i, $routes);
                /* @var $oChildren View */
            }
        }
    }

    /**
     * Go back to the first level, and set all items on the menu route as active.
     *
     * @access   private
     * @param    array $route
     * @since    1.0.0-dev
     * @version  1.0.0-dev
     */
    private function goBackAndSetActive(array &$route)
    {
        if(!isset($route['classes'])) {
            $route['classes'] = [];
        }

        $route['classes'][] = 'current '.$this->activeTrailClass;

        if(isset($route['parent_key'])) {
            $parentsKey  = $route['parent_key'];
            $parentRoute = &$route['parent'][$parentsKey];

            $parentRoute['classes'][] = $this->activeTrailClass;

            if(isset($parentRoute['parent'])) {
                $this->goBackAndSetActive($parentRoute);
            }
        }
    }

    /**
     * Change active trail class.
     *
     * @access   public
     * @param    string $class
     * @return   $this
     * @since    1.3.0-dev
     * @version  1.3.0-dev
     */
    public function setActiveTrailClass($class)
    {
        if(empty($class)) {
            return \Plethora\Exception\Fatal(__('Active trail class cannot be empty!'));
        }

        $this->activeTrailClass = $class;

        return $this;
    }

    public function setSubmenuClass($class)
    {
        $this->submenuClasses = $class;

        return $this;
    }

    /**
     * Get menu attributes.
     *
     * @access   public
     * @return   Helper\Attributes
     * @since    1.3.0-dev
     * @version  1.3.0-dev
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}
