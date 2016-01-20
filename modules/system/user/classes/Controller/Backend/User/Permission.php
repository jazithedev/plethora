<?php

namespace Controller\Backend\User;

use Controller;

/**
 * @author         Krzysztof Trzos
 * @package        user
 * @subpackage     controller/backend/user
 * @since          2.20.1, 2015-01-10
 * @version        1.1.0-dev, 2015-08-09
 */
class Permission extends Controller\Backend
{
    /**
     * List of permissions related to particular Actions
     *
     * @access    protected
     * @var        array
     * @since     1.1.0-dev, 2015-08-09
     */
    protected $aPermissions = [
        'list'   => 'permissions',
        'add'    => 'permissions',
        'edit'   => 'permissions',
        'delete' => 'permissions_del',
    ];

    /**
     * List of columns available in items list.
     *
     * @access    protected
     * @var        array
     * @since     1.0.0-dev, 2015-01-10
     */
    protected $listColumns = ['id', 'name'];

    /**
     * Model class for particular backend.
     *
     * @access    protected
     * @var        string
     * @since     1.0.0-dev, 2015-01-10
     */
    protected $sModel = '\Model\User\Permission';
}