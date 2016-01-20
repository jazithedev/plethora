<?php

namespace Plethora\Exception;

use Plethora\Exception;
use Controller\Frontend;

/**
 * @package        Plethora
 * @subpackage     Exception
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Code404 extends Exception {

    /**
     * @access    protected
     * @var        string
     * @since   1.0.0-alpha
     */
    protected $sHeaderContent = '404 Not Found';

    /**
     * @access    protected
     * @var        integer
     * @since     1.0.0-alpha
     */
    protected $iHttpCode = 404;

    /**
     * 404 error handler.
     *
     * @access     public
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function handler() {
        header('HTTP/1.0 '.$this->sHeaderContent);

        $oController = new Frontend\Error404();
        $oView       = $oController->actionDefault();

        echo $oController->createResponse($oView);

        exit;
    }

}
