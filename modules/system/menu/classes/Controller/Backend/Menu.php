<?php

namespace Controller\Backend;

use Controller;
use Plethora\Route;
use Plethora\View;

/**
 * @author           Krzysztof Trzos
 * @copyright    (c) 2015, Krzysztof Trzos
 * @package          banners
 * @subpackage       controller/backend
 * @since            1.1.2-dev, 2015-08-19
 * @version          1.1.2-dev, 2015-08-19
 */
class Menu extends Controller\Backend
{
    /**
     * @access  protected
     * @var     array
     * @since   1.1.2-dev, 2015-08-19
     */
    protected $listColumns = ['title', 'working_name'];

    /**
     * @access  protected
     * @var     string
     * @since   1.1.2-dev, 2015-08-19
     */
    protected $sModel = '\Model\Menu';

    /**
     * Permissions prefix.
     *
     * @access  protected
     * @var     string
     * @since   1.1.2-dev, 2015-08-19
     */
    protected static $sPermissionsPrefix = 'menu';

    /**
     * ACTION - Contents list.
     *
     * @access   public
     * @return   View
     * @since    1.1.2-dev, 2015-08-19
     * @version  1.1.2-dev, 2015-08-19
     */
    public function actionList()
    {
        $view = parent::actionList();

        $this->addListOption(Route::backendUrl('menu_items', 'list', '{id}'), __('items list'), 'list-alt');

        return $view;
    }

    /**
     *
     * @access   protected
     * @since    1.1.3-dev, 2015-08-20
     * @version  1.1.3-dev, 2015-08-20
     */
    protected function alterBreadcrumbsTitleMain()
    {
        parent::alterBreadcrumbsTitleMain();

        $this->removeBreadcrumb();
        $this->addBreadCrumb(__('Menu list'), Route::backendUrl('menu', 'list'));
    }
}