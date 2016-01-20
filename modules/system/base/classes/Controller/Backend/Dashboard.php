<?php

namespace Controller\Backend;

use Controller\Backend;
use Plethora\Route;
use Plethora\View;

/**
 * Main controller for the dashboard on backend side of the web application.
 *
 * @author      Krzysztof Trzos
 * @package     base
 * @subpackage  controller/backend
 * @since       1.0.0-alpha
 * @version     1.0.0-alpha
 */
class Dashboard extends Backend
{
    /**
     * Default action for the dashboard.
     *
     * @access   public
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function actionDefault()
    {
        $this->addToTitle('Dashboard');
        $this->addCssByTheme('/packages/gridster/jquery.gridster.css', '_common');
        $this->addJsByTheme('/packages/gridster/jquery.gridster.min.js', '_common');

        return View::factory('base/backend/dashboard');
    }

    /**
     * ACTION - Contents list.
     *
     * @access   public
     * @return   View
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function actionList()
    {
        Route::factory('backend')->redirectTo();
    }


}