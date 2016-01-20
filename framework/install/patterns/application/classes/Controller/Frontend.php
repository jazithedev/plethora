<?php

namespace Controller;

use Plethora\Controller;
use Plethora\Route;
use Plethora\Theme;
use Plethora\View;

/**
 * Frontend constructor.
 *
 * @author         Krzysztof Trzos
 * @package        Plethora
 * @subpackage     Controller
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Frontend extends Controller
{
    /**
     * Costructor.
     *
     * @access     public
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function __construct()
    {
        // initialize theme
        Theme::initFrontend();

        // parent consturctor
        parent::__construct();

        // add CSS & JS files for all subpages
        $this->addCssByTheme('/packages/bootstrap/css/bootstrap.css', '_common');
        $this->addJsByTheme('/js/jquery-2.1.3.min.js', '_common');
        $this->addJsByTheme('/js/jquery-ui.min.js', '_common');
        $this->addJsByTheme('/packages/bootstrap/js/bootstrap.min.js', '_common');

        // add first breadcrumb
        $frontPageURL = Route::factory('home')->url();
        $this->addBreadCrumb(__('Front page'), $frontPageURL);
    }

    /**
     * Default constructor action.
     *
     * @access     public
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function actionDefault()
    {
        return View::factory('base/front_page');
    }
}
