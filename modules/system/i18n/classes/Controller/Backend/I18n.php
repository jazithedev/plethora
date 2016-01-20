<?php

namespace Controller\Backend;

use Controller;
use Plethora\I18n as I18nTools;
use Plethora\Cache;
use Plethora\Route;
use Plethora\Router;
use Plethora\Session;
use Plethora\View;
use Plethora\Exception;

/**
 * @author           Krzysztof Trzos
 * @copyright    (c) 2013, Krzysztof Trzos
 * @package          Controller
 * @subpackage       Backend
 * @since            2.5.1, 2013-12-28
 * @version          1.0.5-dev, 2015-06-07
 */
class I18n extends Controller\Backend
{
    /**
     * Change main (start or parent) breadcrumbs and/or title for this backend
     * constructor.
     *
     * @access   protected
     * @since    1.2.0-dev
     * @version  1.2.0-dev
     */
    protected function alterBreadcrumbsTitleMain()
    {
        // parent call
        parent::alterBreadcrumbsTitleMain();

        // remove last breadcrumb
        $this->removeBreadcrumb();

        // add new breadcrumb
        $sControllerOriginal = Router::getControllerName();
        $sURL                = Route::backendUrl($sControllerOriginal, 'index');
        $sControllerName     = strtolower($sControllerOriginal);

        $this->addBreadCrumb(__('section.'.$sControllerName), $sURL);
    }


    /**
     * ACTION - Index action.
     *
     * @access   public
     * @return   View
     * @since    1.2.0-dev
     * @version  1.2.0-dev
     */
    public function actionIndex()
    {
        $this->setTitle(__('Interface translations'));

        $info = Cache::get('info', 'i18n');

        return View::factory('i18n/backend/index')
            ->bind('info', $info);
    }

    /**
     * ACTION which is used to clear all languages cache.
     *
     * @access     public
     * @since      1.2.0-dev
     * @version    1.2.0-dev
     */
    public function actionReloadCache()
    {
        try {
            I18nTools\Core::reloadCache();

            $msg     = __('Cache has been successfully reloaded.');
            $msgType = 'success';
        } catch(Exception\Fatal\I18n $e) {
            $msg     = '<b>Error occured while reloading translation cache:</b> <br />'.$e->getMessage();
            $msgType = 'danger';
        }

        $sURL = Route::factory('backend')->url([
            'controller' => 'i18n',
            'action'     => 'index',
        ]);

        Session::flash($sURL, $msg, $msgType);
    }

    /**
     * Method used to generate code redirecting to main subpage of this
     * module.
     *
     * @access   private
     * @throws   Exception\Router
     * @since    1.2.0-dev
     * @version  1.2.0-dev
     */
    private static function reloadToIndex()
    {
        Route::factory('backend')->redirectTo([
            'controller' => 'i18n',
            'action'     => 'index',
        ]);
    }

    /**
     * ACTION - Contents list.
     *
     * @access   public
     * @since    1.2.0-dev
     * @version  1.2.0-dev
     */
    public function actionList()
    {
        self::reloadToIndex();
    }

    /**
     * ACTION - Backend add action.
     *
     * @access   public
     * @since    1.2.0-dev
     * @version  1.2.0-dev
     */
    public function actionAdd()
    {
        self::reloadToIndex();
    }

    /**
     * ACTION - Backend edit action.
     *
     * @access   public
     * @since    1.2.0-dev
     * @version  1.2.0-dev
     */
    public function actionEdit()
    {
        self::reloadToIndex();
    }

    /**
     * ACTION - Backend delete action.
     *
     * @access   public
     * @since    1.2.0-dev
     * @version  1.2.0-dev
     */
    public function actionDelete()
    {
        self::reloadToIndex();
    }

    /**
     * ACTION - Contents list.
     *
     * @access   public
     * @since    1.2.0-dev
     * @version  1.2.0-dev
     */
    public function actionSort()
    {
        self::reloadToIndex();
    }
}