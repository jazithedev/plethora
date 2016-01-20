<?php

namespace Controller\Frontend;

use Controller\Frontend;
use Plethora\View;

/**
 * Main frontend controller for 401 Forbidden error.
 *
 * @package          news
 * @subpackage       controller
 * @author           Krzysztof Trzos
 * @copyright    (c) 2016, Krzysztof Trzos
 * @since            1.0.0-alpha
 * @version          1.0.0-alpha
 */
class Error401 extends Frontend
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
        $this->setTitle(__('Page unauthorized!').' [401]');

        $oContent = View::factory('base/error_pages/401');

        $this->oView->bind('oContent', $oContent);

        return $oContent;
    }
}