<?php

namespace Controller\Backend;

use Controller;
use Plethora\Route;
use Plethora\Router;

/**
 * @author           Krzysztof Trzos
 * @copyright    (c) 2015, Krzysztof Trzos
 * @package          base
 * @subpackage       Controller\Backend
 * @since            1.0.0-alpha
 * @version          1.0.0-alpha
 */
class Files extends Controller\Backend
{
    /**
     * @access  protected
     * @var     array
     * @since   1.0.0-alpha
     */
    protected $listColumns = ['id', 'file_path', 'name', 'ext', 'size'];

    /**
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected $sModel = '\Model\File';

    /**
     * Permissions prefix.
     *
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected static $sPermissionsPrefix = 'files';

    /**
     * Method which can be used to overwrite local actions of list subpage.
     *
     * @access   protected
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    protected function alterListLocalActions()
    {
        // Overwritten to not to show any local actions from
        // base class \Controller\Backend.
    }

    /**
     * ACTION - Backend add action.
     *
     * @access   public
     * @return   void
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function actionAdd()
    {
        $url = Route::factoryBackendURL('files', 'list');

        Router::relocate($url);
    }
}