<?php

namespace Plethora\Router;

use Plethora\Helper;
use Plethora\Route;
use Plethora\Router;
use Plethora\View;

/**
 * @package        Plethora
 * @subpackage     Router
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class LocalActions {

    /**
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private static $aLocalActions = [];

    /**
     * Add new local action.
     *
     * @access   public
     * @param    string $sTitle
     * @param    string $sToRoute
     * @param    string $sRoute
     * @return   LocalActions\Action
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function &addLocalAction($sTitle, $sToRoute, $sRoute) {
        if(!isset(static::$aLocalActions[$sToRoute])) {
            static::$aLocalActions[$sToRoute] = [];
        }

        $iCount = count(static::$aLocalActions[$sToRoute]);

        static::$aLocalActions[$sToRoute][$iCount + 1] = LocalActions\Action::factory($sTitle, $sToRoute, $sRoute);

        return static::$aLocalActions[$sToRoute][$iCount + 1];
    }

    /**
     * Get all loaded local actions.
     *
     * @static
     * @access   public
     * @param    string $sForRoute
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function getLocalActions($sForRoute) {
        return Helper\Arrays::get(static::$aLocalActions, $sForRoute, []);
    }

    /**
     * Generate local actions View.
     *
     * @static
     * @access   public
     * @return   View
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function generateActions() {
        $aActions      = [];
        $sForRoute     = Router::getCurrentRouteName();
        $aLocalActions = static::getLocalActions($sForRoute);

        // create local actions
        foreach($aLocalActions as $oAction) {
            /* @var $oAction LocalActions\Action */
            // check conditions
            $aConditions = $oAction->getConditions();
            $oBuilder    = $oAction->getBuilder();
            $aParams     = $oAction->getParameters();

            if(!empty($aConditions)) {
                foreach($oAction->getConditions() as $sParam => $sParamValue) {
                    if(Router::getParam($sParam) != $sParamValue) {
                        continue 2;
                    }
                }
            }

            // get route and check access to it
            $oRoute = Route::factory($oAction->getRoute());

            if($oRoute->hasAccess($aParams) === FALSE) {
                continue 1;
            }

            // use builder, if set
            if($oBuilder !== NULL) {
                $oBuilder($oAction);
            }

            // create new local action
            $sURL = $oRoute->url($oAction->getParameters());

            $aActions[] = [
                'url'   => $sURL,
                'title' => $oAction->getTitle(),
                'icon'  => $oAction->getIcon(),
            ];
        }

        return View::factory('base/local_actions')
            ->bind('aActions', $aActions);
    }

}
