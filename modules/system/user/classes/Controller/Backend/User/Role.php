<?php

namespace Controller\Backend\User;

use Controller;

/**
 * @author           Krzysztof Trzos
 * @copyright    (c) 2015, Krzysztof Trzos
 * @package          user
 * @subpackage       Controller\Backend
 * @since            1.0.0-dev, 2015-01-10
 * @version          1.1.0-dev, 2015-08-09
 */
class Role extends Controller\Backend
{
    /**
     * List of permissions related to particular Actions
     *
     * @access    protected
     * @var        array
     * @since     1.1.0-dev, 2015-08-09
     */
    protected $aPermissions = [
        'list'   => 'roles',
        'add'    => 'roles',
        'edit'   => 'roles',
        'delete' => 'roles_del',
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
    protected $sModel = '\Model\User\Role';
}