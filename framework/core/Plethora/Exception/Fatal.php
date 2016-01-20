<?php

namespace Plethora\Exception;

use Plethora\Core;
use Plethora\Exception;
use Plethora\View;

/**
 * Class Fatal
 *
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @package        Plethora
 * @subpackage     Exception
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Fatal extends Exception {

    /**
     * Fatal error handler.
     *
     * @access     public
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function handler() {
        if(Core::getAppMode() == Core::MODE_DEVELOPMENT) {
            throw $this;
        } else {
            header('HTTP/1.0 '.$this->sHeaderContent);

            echo View::factory('base/error_pages/500')->render();
            die;
        }
    }

}
