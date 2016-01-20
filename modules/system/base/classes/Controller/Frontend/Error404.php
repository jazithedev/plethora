<?php

namespace Controller\Frontend;

use Controller\Frontend;
use Plethora\View;

/**
 * Main frontend controller for 404 Not Found error.
 *
 * @package          base
 * @subpackage       Controller\Frontend
 * @author           Krzysztof Trzos
 * @copyright    (c) 2016, Krzysztof Trzos
 * @since            1.0.0-alpha
 * @version          1.0.0-alpha
 */
class Error404 extends Frontend
{
    /**
     * Constructor
     *
     * @access   public
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * ACTION - Profile of particular news.
     *
     * @access   public
     * @return   View
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function actionDefault()
    {
        $this->setTitle(__('Page not found!').' [404]');

        $oContent = View::factory('base/error_pages/404');

        $this->oView->bind('oContent', $oContent);

        return $oContent;
    }
}