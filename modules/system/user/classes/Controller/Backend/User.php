<?php

namespace Controller\Backend;

use Controller\Backend;
use Plethora\Route;
use Plethora\Router;
use Plethora\View;

/**
 * @author   Krzysztof Trzos
 * @package  Plethora\Controller
 * @since    1.0.0, 2014-09-27
 * @version  2.1.2-dev
 */
class User extends Backend
{
    /**
     * List of permissions related to particular Actions
     *
     * @access  protected
     * @var     array
     * @since   1.0.0, 2014-09-27
     */
    protected $aPermissions = [
        'list'   => 'users',
        'add'    => 'users_add',
        'edit'   => 'users_edit',
        'delete' => 'users_remove',
    ];

    /**
     * List of columns available in items list.
     *
     * @access  protected
     * @var     array
     * @since   1.0.0, 2014-09-27
     */
    protected $listColumns = ['id', 'login', 'roles'];

    /**
     * Model class for particular backend.
     *
     * @access  protected
     * @var     string
     * @since   1.0.0, 2014-09-27
     */
    protected $sModel = '\Model\User';

    /**
     * ACTION - Backend delete action.
     *
     * @access   public
     * @return   View
     * @since    2.1.2-dev
     * @version  2.1.2-dev
     */
    public function actionDelete()
    {
        // check if someone want to remove admin account
        if((int)Router::getParam('id') === 1) {
            return View::factory('base/alert')
                ->set('sType', 'danger')
                ->set('sMsg', __('Admin account cannot be removed!'));
        }

        // return View
        return parent::actionDelete();
    }


}