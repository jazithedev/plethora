<?php

namespace Controller\Backend\Menu;

use Doctrine;
use Plethora\Exception;
use Plethora\DB;
use Plethora\Route;
use Plethora\Router;
use Plethora\Session;
use Plethora\View;

/**
 * @author           Krzysztof Trzos
 * @copyright    (c) 2015, Krzysztof Trzos
 * @package          banners
 * @subpackage       controller/backend
 * @since            1.1.2-dev, 2015-08-19
 * @version          1.3.0-dev
 */
class Items extends \Controller\Backend
{

    /**
     * @access    protected
     * @var        array
     * @since     1.1.2-dev, 2015-08-19
     */
    protected $listColumns = ['name'];

    /**
     * @access    protected
     * @var        string
     * @since     1.1.2-dev, 2015-08-19
     */
    protected $sModel = '\Model\Menu\Item';

    /**
     * Permissions prefix.
     *
     * @access    protected
     * @var        string
     * @since     1.1.2-dev, 2015-08-19
     */
    protected static $sPermissionsPrefix = 'menu';

    /**
     * ACTION - Contents list.
     *
     * @access   public
     * @return   View
     * @throws   Exception\Code404
     * @throws   Exception\Fatal
     * @since    1.2.0-dev
     * @version  1.2.0-dev
     */
    public function actionList()
    {
        $sMenuID = Router::getParam('id');

        if($sMenuID === FALSE) {
            throw new Exception\Code404();
        }

        return parent::actionList();
    }

    /**
     * Remove particular menu item after form submit (if form data is valid).
     *
     * @access     protected
     * @since      1.2.0-dev
     * @version    1.2.0-dev
     */
    protected function alterDelete()
    {
        $item = $this->getModel();
        /* @var $item \Model\Menu\Item */

        $item->remove();
        DB::flush();

        $controller = Router::getParam('controller');
        $id         = $item->getMenu()->getId();
        $url        = Route::backendUrl($controller, 'list', $id);

        Session::flash($url, __('Menu item has been deleted successfully.'));
    }

    /**
     * Method to overwrite if a list query should be altered before sending
     * request to database.
     *
     * @access     protected
     * @param    Doctrine\ORM\QueryBuilder $oListQuery
     * @since      1.1.3-dev, 2015-08-20
     * @version    1.1.3-dev, 2015-08-20
     */
    protected function alterListQuery(Doctrine\ORM\QueryBuilder $oListQuery)
    {
        $iMenuID = Router::getParam('id');

        $oListQuery->andWhere('t.menu = :menuid');
        $oListQuery->setParameter('menuid', $iMenuID);

        parent::alterListQuery($oListQuery);
    }

    /**
     * Method which can be used to overwrite local actions of list subpage.
     *
     * @access     protected
     * @since      1.1.3-dev, 2015-08-20
     * @version    1.1.6-dev, 2015-08-23
     */
    protected function alterListLocalActions()
    {
        $iMenuID          = Router::getParam('id');
        $sControllerParam = Router::getParam('controller');

        Router\LocalActions::addLocalAction(__('Add menu.'.$sControllerParam), 'backend', 'backend')
            ->setConditions([
                'controller' => $sControllerParam,
                'action'     => 'list',
            ])
            ->setParameters([
                'controller' => $sControllerParam,
                'action'     => 'add',
                'id'         => $iMenuID,
            ]);

        Router\LocalActions::addLocalAction(__('Sort menu.'.$sControllerParam), 'backend', 'backend')
            ->setConditions([
                'controller' => $sControllerParam,
                'action'     => 'list',
            ])
            ->setParameters([
                'controller' => $sControllerParam,
                'action'     => 'sort',
                'id'         => $iMenuID,
            ])
            ->setIcon('sort');
    }

    /**
     * Change main (start or parent) breadcrumbs and/or title for this backend
     * constructor.
     *
     * @access     protected
     * @since      1.1.3-dev, 2015-08-20
     * @version    1.2.0-dev
     */
    protected function alterBreadcrumbsTitleMain()
    {
        parent::alterBreadcrumbsTitleMain();

        $this->setTitle(__('Management panel').' - ');

        $iMenuID = Router::getParam('id');
        $sAction = Router::getParam('action');

        if($iMenuID === FALSE || $sAction === FALSE) {
            throw new Exception\Code404();
        }

        if($sAction === 'edit') {
            $oItem = DB::find('\Model\Menu\Item', $iMenuID);
            /* @var $oItem \Model\Menu\Item */
            $iMenuID = $oItem->getMenu()->getId();
        }

        $this->removeBreadcrumb();
        $this->addBreadCrumb(__('Menu list'), Route::backendUrl('menu', 'list'));
        $this->addBreadCrumb(__('Menu items list'), Route::backendUrl(Router::getControllerName(), 'list', $iMenuID));
    }

    /**
     * Change breadcrumbs and/or title of "edit entity" action.
     *
     * @access     protected
     * @since      1.1.3-dev, 2015-08-20
     * @version    1.2.0-dev
     */
    protected function alterBreadcrumbsTitleEdit()
    {
        $this->addToTitle(__('Edit menu item'));
        $this->addBreadCrumb(__('Edit menu item'), Router::getCurrentUrl());
    }

    /**
     * Change breadcrumbs and/or title of "add entity" action.
     *
     * @access     protected
     * @since      1.1.3-dev, 2015-08-20
     * @version    1.2.0-dev
     */
    protected function alterBreadcrumbsTitleAdd()
    {
        $this->addToTitle(__('Add menu item'));
        $this->addBreadCrumb(__('Add menu item'), Router::getCurrentUrl());
    }

    /**
     * Method which can be used to overwrite local actions of list subpage.
     *
     * @access     protected
     * @since      1.1.3-dev, 2015-08-20
     * @version    1.2.0-dev
     */
    protected function alterSortLocalActions()
    {
        $iMenuID          = Router::getParam('id');
        $sControllerParam = Router::getParam('controller');

        Router\LocalActions::addLocalAction(__('Add menu.'.$sControllerParam), 'backend', 'backend')
            ->setConditions([
                'controller' => $sControllerParam,
                'action'     => 'sort',
                'id'         => $iMenuID,
            ])
            ->setParameters([
                'controller' => $sControllerParam,
                'action'     => 'add',
                'id'         => $iMenuID,
            ]);

        Router\LocalActions::addLocalAction(__('menu.'.$sControllerParam.' list'), 'backend', 'backend')
            ->setIcon('list-alt')
            ->setConditions([
                'controller' => $sControllerParam,
                'action'     => 'sort',
                'id'         => $iMenuID,
            ])
            ->setParameters([
                'controller' => $sControllerParam,
                'action'     => 'list',
                'id'         => $iMenuID,
            ]);
    }

    /**
     * Generate result (containing entities list) for sort list.
     *
     * @access     protected
     * @return    array
     * @since      1.2.0-dev
     * @version    1.2.0-dev
     */
    protected function alterSortQueryResult()
    {
        $iMenuID     = Router::getParam('id');
        $sModelClass = $this->getModel()->getClass();

        return DB::query('SELECT t FROM '.$sModelClass.' t WHERE t.menu = :menu_id ORDER BY t.order_number')
            ->param('menu_id', $iMenuID)
            ->execute();
    }

}
