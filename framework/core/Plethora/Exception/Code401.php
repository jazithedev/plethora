<?php

namespace Plethora\Exception;

use Plethora\Exception;
use Controller\Frontend;

/**
 * The request requires user authentication. The response MUST include a
 * WWW-Authenticate header field containing a challenge applicable to the
 * requested resource.
 *
 * @package        Plethora
 * @subpackage     Exception
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Code401 extends Exception {

    /**
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected $sHeaderContent = '401 Unauthorized';

    /**
     * @access  protected
     * @var     integer
     * @since   1.0.0-alpha
     */
    protected $iHttpCode = 401;

    /**
     * 401 error handler.
     *
     * @access     public
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function handler() {
        header('HTTP/1.0 '.$this->sHeaderContent);

        $oController = new Frontend\Error401();
        $oView       = $oController->actionDefault();

        echo $oController->createResponse($oView);

        exit;
    }

}
