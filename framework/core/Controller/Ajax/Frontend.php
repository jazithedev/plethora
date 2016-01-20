<?php

namespace Controller\Ajax;

use Controller\Ajax;
use Plethora\Theme;

/**
 * Parent controller for AJAX requests.
 *
 * @author         Krzysztof Trzos
 * @package        Plethora
 * @subpackage     Controller\Ajax
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Frontend extends Ajax {
    /**
     * Constructor.
     *
     * @access     public
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function __construct() {
        parent::__construct();

        Theme::initFrontend();
    }
}