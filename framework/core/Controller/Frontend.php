<?php

namespace Controller;

use Plethora\Controller;
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
